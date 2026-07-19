<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\PresensiSiswa;
use App\Models\Jadwal;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Izin;
use App\Models\Kegiatan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\GuruKelasMapel;
use App\Models\Views\VRekapPresensiSiswa;
use App\Models\Views\VStatistikKehadiranKelas;
use App\Services\DatabaseProcedureService;
use App\Services\LogActivityService;
use Illuminate\Support\Facades\Storage;

class GuruController extends Controller
{
    protected $dbProcedure;
    protected $logActivity;

    public function __construct(DatabaseProcedureService $dbProcedure, LogActivityService $logActivity)
    {
        $this->dbProcedure = $dbProcedure;
        $this->logActivity = $logActivity;
    }

    public function dashboard()
    {
        $guru = auth()->user()->guru;
        
        // Jika guru tidak ditemukan, redirect ke login
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data guru tidak ditemukan');
        }
        
        $jadwalHariIni = Jadwal::where('id_guru', $guru->id_guru)
            ->where('hari', now()->locale('id')->dayName)
            ->with('kelas')
            ->get();

        $totalKelas = Jadwal::where('id_guru', $guru->id_guru)
            ->distinct('id_kelas')
            ->count('id_kelas');

        $totalMateri = Materi::where('id_guru', $guru->id_guru)->count();
        $totalTugas = Tugas::where('id_guru', $guru->id_guru)->count();

        // Ambil presensi hari ini
        $today = date('Y-m-d');
        $userId = auth()->user()->id_user;
        $presensiHariIni = Presensi::where('id_user', $userId)
            ->where('tanggal', $today)
            ->first();

        // Jam kerja standar (07:30 untuk guru)
        $jamKerjaStandar = '07:30:00';
        $statusKehadiran = 'Belum Absen';
        
        if ($presensiHariIni && !is_null($presensiHariIni->jam_masuk)) {
            $jamMasuk = $presensiHariIni->jam_masuk;
            $jamMasukTime = \Carbon\Carbon::createFromFormat('H:i:s', $jamMasuk);
            $jamStandarTime = \Carbon\Carbon::createFromFormat('H:i:s', $jamKerjaStandar);
            
            if ($jamMasukTime->lessThanOrEqualTo($jamStandarTime)) {
                $statusKehadiran = 'Anda Tepat Waktu';
            } else {
                // Hitung selisih waktu
                $diff = $jamMasukTime->diff($jamStandarTime);
                $jam = $diff->h;
                $menit = $diff->i;
                $detik = $diff->s;
                $statusKehadiran = "Anda Terlambat {$jam} jam {$menit} menit {$detik} detik";
            }
        }

        // Tentukan status absen
        $statusAbsen = [
            'sudah_masuk' => $presensiHariIni && !is_null($presensiHariIni->jam_masuk),
            'sudah_keluar' => $presensiHariIni && !is_null($presensiHariIni->jam_keluar),
            'jam_masuk' => $presensiHariIni->jam_masuk ?? null,
            'jam_keluar' => $presensiHariIni->jam_keluar ?? null,
            'status_kehadiran' => $statusKehadiran,
        ];

        $view = view('guru.dashboard', compact('jadwalHariIni', 'totalKelas', 'totalMateri', 'totalTugas', 'statusAbsen'));
        return response($view)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function kelasAmpuan()
    {
        $guru = auth()->user()->guru;
        
        // Ambil semua kelas yang diampu guru dengan jadwalnya
        $kelasAmpuan = Jadwal::where('id_guru', $guru->id_guru)
            ->with('kelas')
            ->distinct()
            ->get(['id_kelas', 'id_guru'])
            ->unique('id_kelas');
        
        // Untuk setiap kelas, ambil jadwal lengkapnya
        $kelasDetail = [];
        foreach ($kelasAmpuan as $item) {
            $jadwal = Jadwal::where('id_kelas', $item->id_kelas)
                ->where('id_guru', $guru->id_guru)
                ->with('kelas')
                ->get();
            
            if ($jadwal->count() > 0) {
                $kelasDetail[] = [
                    'id_kelas' => $item->id_kelas,
                    'nama_kelas' => $jadwal->first()->kelas->nama_kelas,
                    'jumlah_siswa' => Siswa::where('id_kelas', $item->id_kelas)->count(),
                    'jadwal' => $jadwal
                ];
            }
        }
        
        return view('guru.kelas.index', compact('kelasDetail'));
    }

    public function getServerTime()
    {
        return response()->json([
            'jam_sekarang' => now()->format('H:i:s'),
            'timestamp' => now()->timestamp
        ]);
    }

    public function absenMasuk()
    {
        $today = date('Y-m-d');
        $userId = auth()->user()->id_user;
        $jamSekarang = now()->format('H:i:s'); // Gunakan Carbon timezone-aware

        try {
            // Cek apakah sudah absen masuk hari ini
            $presensiHariIni = Presensi::where('id_user', $userId)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            if ($presensiHariIni && !is_null($presensiHariIni->jam_masuk)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Anda sudah absen masuk pada jam ' . $presensiHariIni->jam_masuk . '. Silakan absen keluar terlebih dahulu sebelum absen masuk lagi.'
                ]);
            }

            // Buat atau update presensi - SELALU update dengan jam sekarang
            $presensi = Presensi::updateOrCreate(
                [
                    'id_user' => $userId,
                    'tanggal' => $today,
                ],
                [
                    'jam_masuk' => $jamSekarang,
                    'jam_keluar' => null,  // Reset jam keluar
                    'status' => 'hadir',
                ]
            );

            $this->logActivity->log('presensi_masuk', $userId, 'Absen masuk - ' . $jamSekarang);

            return response()->json([
                'success' => true,
                'message' => '✅ Absen masuk berhasil dicatat pada jam ' . $jamSekarang,
                'jam_masuk' => $jamSekarang,
                'jam_keluar' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function absenKeluar()
    {
        $today = date('Y-m-d');
        $userId = auth()->user()->id_user;
        $jamSekarang = now()->format('H:i:s'); // Gunakan Carbon timezone-aware

        try {
            // Cek presensi hari ini
            $presensiHariIni = Presensi::where('id_user', $userId)
                ->where('tanggal', $today)
                ->lockForUpdate()
                ->first();

            // Validasi: harus sudah absen masuk
            if (!$presensiHariIni || is_null($presensiHariIni->jam_masuk)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Anda belum absen masuk. Silakan absen masuk terlebih dahulu'
                ]);
            }

            // Validasi: belum absen keluar
            if (!is_null($presensiHariIni->jam_keluar)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Anda sudah absen keluar pada jam ' . $presensiHariIni->jam_keluar
                ]);
            }

            // Update dengan jam_keluar - SELALU update dengan jam sekarang
            $presensi = $presensiHariIni->update([
                'jam_keluar' => $jamSekarang,
            ]);

            $this->logActivity->log('presensi_keluar', $userId, 'Absen keluar - ' . $jamSekarang);

            return response()->json([
                'success' => true,
                'message' => '✅ Absen keluar berhasil dicatat pada jam ' . $jamSekarang,
                'jam_masuk' => $presensiHariIni->jam_masuk,
                'jam_keluar' => $jamSekarang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function presensiSiswa()
    {
        $guru = auth()->user()->guru;
        $jadwal = Jadwal::where('id_guru', $guru->id_guru)->with('kelas')->get();
        
        return view('guru.presensi-siswa', compact('jadwal'));
    }

    public function inputPresensiSiswa(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_pelajaran,id_jadwal',
            'tanggal' => 'required|date',
            'siswa' => 'required|array',
        ]);

        foreach ($request->siswa as $idSiswa => $status) {
            PresensiSiswa::updateOrCreate(
                [
                    'id_siswa' => $idSiswa,
                    'id_jadwal' => $request->id_jadwal,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'status' => $status,
                    'diinput_oleh' => auth()->user()->id_user,
                ]
            );
        }

        $this->logActivity->log('presensi_siswa', auth()->user()->id_user, 'Input presensi siswa');

        return redirect()->back()->with('success', 'Presensi siswa berhasil dicatat');
    }

    public function rekapPresensiSiswa(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $guru = auth()->user()->guru;
        $kelasIds = Jadwal::where('id_guru', $guru->id_guru)->pluck('id_kelas');

        $rekap = VRekapPresensiSiswa::whereIn('id_kelas', $kelasIds)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('guru.rekap-presensi-siswa', compact('rekap', 'bulan', 'tahun'));
    }

    public function statistikKelas($idKelas)
    {
        $bulan = request()->input('bulan', date('m'));
        $tahun = request()->input('tahun', date('Y'));

        $statistik = VStatistikKehadiranKelas::where('id_kelas', $idKelas)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        $kelas = Kelas::findOrFail($idKelas);

        return view('guru.statistik-kelas', compact('statistik', 'kelas', 'bulan', 'tahun'));
    }

    public function materi()
    {
        $guru = auth()->user()->guru;
        $materi = Materi::where('id_guru', $guru->id_guru)
            ->with(['kelas', 'guru'])
            ->orderBy('uploaded_at', 'desc')
            ->paginate(20);

        return view('guru.materi.materi-list', compact('materi'));
    }

    public function createMateri()
    {
        $guru = auth()->user()->guru;
        
        // Priority 1: Ambil dari jadwal (paling reliable)
        $kelasIds = Jadwal::where('id_guru', $guru->id_guru)
            ->distinct('id_kelas')
            ->pluck('id_kelas');
        
        // Priority 2: Jika jadwal kosong, coba dari guru_kelas_mapel
        if ($kelasIds->isEmpty()) {
            $kelasIds = GuruKelasMapel::where('id_guru', $guru->id_guru)
                ->distinct('id_kelas')
                ->pluck('id_kelas');
        }
        
        // Priority 3: Jika masih kosong, coba tanpa filter tahun ajaran aktif
        if ($kelasIds->isEmpty()) {
            $kelasIds = GuruKelasMapel::where('id_guru', $guru->id_guru)
                ->distinct('id_kelas')
                ->pluck('id_kelas');
        }
        
        // Ambil data kelas
        $kelas = Kelas::whereIn('id_kelas', $kelasIds)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('guru.materi.create', compact('kelas'));
    }

    public function storeMateri(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'mata_pelajaran' => 'required|string|max:100',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        $filename = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('materi', $filename, 'public');
        }

        $materi = Materi::create([
            'id_guru' => auth()->user()->guru->id_guru,
            'id_kelas' => $request->id_kelas,
            'judul_materi' => $request->judul,
            'mata_pelajaran' => $request->mata_pelajaran,
            'deskripsi' => $request->deskripsi,
            'file_materi' => $filename,
        ]);

        $this->logActivity->logCrud('create', auth()->user()->id_user, 'materi', $materi->id_materi);

        return redirect()->route('guru.materi')->with('success', 'Materi berhasil diupload');
    }

    public function deleteMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        if ($materi->id_guru !== auth()->user()->guru->id_guru) {
            abort(403);
        }

        if ($materi->file_materi && Storage::disk('public')->exists('materi/' . $materi->file_materi)) {
            Storage::disk('public')->delete('materi/' . $materi->file_materi);
        }

        $materi->delete();
        $this->logActivity->logCrud('delete', auth()->user()->id_user, 'materi', $id);

        return redirect()->route('guru.materi')->with('success', 'Materi berhasil dihapus');
    }

    public function bulkDeleteMateri(Request $request)
    {
        $ids = json_decode($request->ids);
        $guru = auth()->user()->guru;
        
        $deleted = 0;
        foreach ($ids as $id) {
            $materi = Materi::find($id);
            if ($materi && $materi->id_guru === $guru->id_guru) {
                if ($materi->file_materi && Storage::disk('public')->exists('materi/' . $materi->file_materi)) {
                    Storage::disk('public')->delete('materi/' . $materi->file_materi);
                }
                $materi->delete();
                $this->logActivity->logCrud('delete', auth()->user()->id_user, 'materi', $id);
                $deleted++;
            }
        }
        
        return redirect()->route('guru.materi')->with('success', $deleted . ' materi berhasil dihapus');
    }

    public function tugas()
    {
        $guru = auth()->user()->guru;
        $tugas = Tugas::where('id_guru', $guru->id_guru)
            ->with(['kelas', 'pengumpulan'])
            ->orderBy('deadline', 'desc')
            ->get();

        return view('guru.tugas.tugas-guru-list', compact('tugas'));
    }

    public function createTugas()
    {
        $guru = auth()->user()->guru;
        
        // Ambil dari guru_kelas_mapel
        $kelasMapelData = GuruKelasMapel::forGuruActiveYear($guru->id_guru)
            ->get()
            ->unique('id_kelas');
        
        // Ambil dari jadwal
        $jadwalData = Jadwal::where('id_guru', $guru->id_guru)
            ->get()
            ->unique('id_kelas');
        
        // Merge kedua data
        $allData = $kelasMapelData->merge($jadwalData);
        $kelasIds = $allData->unique('id_kelas')->pluck('id_kelas');
        
        $kelas = Kelas::whereIn('id_kelas', $kelasIds)
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('guru.tugas.create', compact('kelas'));
    }

    public function storeTugas(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'judul_tugas' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date|after:now',
        ]);

        $tugas = Tugas::create([
            'id_guru' => auth()->user()->guru->id_guru,
            'id_kelas' => $request->id_kelas,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
        ]);

        $this->logActivity->logCrud('create', auth()->user()->id_user, 'tugas', $tugas->id_tugas);

        return redirect()->route('guru.tugas')->with('success', 'Tugas berhasil ditambahkan');
    }

    public function detailTugas($id)
    {
        $tugas = Tugas::with(['kelas', 'pengumpulan.siswa.user'])->findOrFail($id);
        
        if ($tugas->id_guru !== auth()->user()->guru->id_guru) {
            abort(403);
        }

        return view('guru.tugas.detail', compact('tugas'));
    }

    public function nilaiTugas(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan = PengumpulanTugas::findOrFail($id);
        $pengumpulan->update([
            'nilai' => $request->nilai,
            'feedback_guru' => $request->feedback,
        ]);

        $this->logActivity->log('penilaian', auth()->user()->id_user, 'Menilai tugas ID: ' . $id);

        return redirect()->back()->with('success', 'Nilai berhasil diberikan');
    }

    public function deleteTugas($id)
    {
        $tugas = Tugas::findOrFail($id);
        
        // Check if user is owner
        if ($tugas->id_guru !== auth()->user()->guru->id_guru) {
            abort(403);
        }

        // Delete all pengumpulan first
        PengumpulanTugas::where('id_tugas', $id)->delete();
        
        // Delete tugas
        $tugas->delete();

        $this->logActivity->logCrud('delete', auth()->user()->id_user, 'tugas', $id);

        return redirect()->route('guru.tugas')->with('success', 'Tugas berhasil dihapus');
    }

    public function izin()
    {
        $guru = auth()->user()->guru;

        // Filter izin yang diajukan ke guru ini
        $izinList = Izin::where('id_guru', $guru->id_guru)
            ->with(['user.siswa.kelas', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('guru.izin', compact('izinList'));
    }

    public function approveIzin(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $this->dbProcedure->approveIzin(
            $id,
            auth()->user()->id_user,
            $request->status,
            $request->catatan
        );

        $this->logActivity->logApprovalIzin(auth()->user()->id_user, $id, $request->status);

        return redirect()->back()->with('success', 'Status izin berhasil diupdate');
    }

    public function kegiatan()
    {
        // Auto-update status kegiatan berdasarkan waktu
        $this->autoUpdateStatusKegiatan();
        
        $filter = request('filter', 'upcoming'); // 'upcoming' atau 'history'
        
        $query = Kegiatan::with('pembuatKegiatan');
        
        if ($filter === 'history') {
            $query->where('status', 'completed');
            $orderBy = 'desc';
        } else {
            $query->whereIn('status', ['planned', 'ongoing']);
            $orderBy = 'asc';
        }
        
        $kegiatan = $query->orderBy('tanggal_mulai', $orderBy)->paginate(20);
        
        return view('guru.kegiatan.index', compact('kegiatan', 'filter'));
    }

    private function autoUpdateStatusKegiatan()
    {
        $now = now();
        
        // Update status ke 'completed' jika waktu selesai sudah lewat
        Kegiatan::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->where(function($query) use ($now) {
                $query->where('tanggal_selesai', '<', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_selesai', '=', $now->format('Y-m-d'))
                          ->where('waktu_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'completed']);
        
        // Update status ke 'ongoing' jika waktu mulai sudah tiba tapi belum selesai
        Kegiatan::where('status', 'planned')
            ->where(function($query) use ($now) {
                $query->where('tanggal_mulai', '<', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_mulai', '=', $now->format('Y-m-d'))
                          ->where('waktu_mulai', '<=', $now->format('H:i:s'));
                    });
            })
            ->where(function($query) use ($now) {
                $query->where('tanggal_selesai', '>', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_selesai', '=', $now->format('Y-m-d'))
                          ->where('waktu_selesai', '>=', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'ongoing']);
    }

    public function detailKegiatan($id)
    {
        $kegiatan = Kegiatan::with('pembuatKegiatan')->findOrFail($id);
        return view('guru.kegiatan.detail', compact('kegiatan'));
    }

    /**
     * Halaman Laporan Bulanan - Rekap Kehadiran Guru
     */
    public function laporanBulanan(Request $request)
    {
        $userId = auth()->user()->id_user;
        $guru = auth()->user()->guru;
        
        // Bulan dan tahun dari request atau default bulan ini
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Ambil data presensi bulan yang dipilih
        $presensi = Presensi::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Hitung statistik
        $totalHadir = $presensi->where('status', 'hadir')->count();
        $totalIzin = $presensi->where('status', 'izin')->count();
        $totalSakit = $presensi->where('status', 'sakit')->count();
        $totalAlpha = $presensi->where('status', 'alpha')->count();
        
        // Hitung hari kerja dalam bulan (Senin-Sabtu)
        $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $hariKerja = 0;
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Senin = 1, Sabtu = 6, Minggu = 0
            if ($date->dayOfWeek >= 1 && $date->dayOfWeek <= 6) {
                $hariKerja++;
            }
        }
        
        // Hitung keterlambatan
        $jamKerjaStandar = '07:30:00';
        $terlambat = 0;
        $totalMenitTerlambat = 0;
        
        foreach ($presensi->where('status', 'hadir') as $p) {
            if ($p->jam_masuk && $p->jam_masuk > $jamKerjaStandar) {
                $terlambat++;
                $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $p->jam_masuk);
                $jamStandar = \Carbon\Carbon::createFromFormat('H:i:s', $jamKerjaStandar);
                $totalMenitTerlambat += $jamMasuk->diffInMinutes($jamStandar);
            }
        }
        
        // Persentase kehadiran
        $persentaseKehadiran = $hariKerja > 0 ? round(($totalHadir / $hariKerja) * 100, 1) : 0;
        
        // Data untuk chart
        $statistik = [
            'hadir' => $totalHadir,
            'izin' => $totalIzin,
            'sakit' => $totalSakit,
            'alpha' => $totalAlpha,
            'terlambat' => $terlambat,
            'total_menit_terlambat' => $totalMenitTerlambat,
            'hari_kerja' => $hariKerja,
            'persentase_kehadiran' => $persentaseKehadiran,
        ];
        
        // List bulan untuk dropdown
        $listBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        // List tahun (3 tahun terakhir)
        $listTahun = range(date('Y'), date('Y') - 2);
        
        return view('guru.laporan-bulanan', compact(
            'presensi', 'statistik', 'bulan', 'tahun', 'listBulan', 'listTahun', 'guru'
        ));
    }

    /**
     * Download Laporan Bulanan PDF
     */
    public function downloadLaporanBulanan(Request $request)
    {
        $userId = auth()->user()->id_user;
        $guru = auth()->user()->guru;
        $user = auth()->user();
        
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Ambil data presensi
        $presensi = Presensi::where('id_user', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Hitung statistik
        $totalHadir = $presensi->where('status', 'hadir')->count();
        $totalIzin = $presensi->where('status', 'izin')->count();
        $totalSakit = $presensi->where('status', 'sakit')->count();
        $totalAlpha = $presensi->where('status', 'alpha')->count();
        
        // Hitung hari kerja
        $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        $hariKerja = 0;
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->dayOfWeek >= 1 && $date->dayOfWeek <= 6) {
                $hariKerja++;
            }
        }
        
        $persentaseKehadiran = $hariKerja > 0 ? round(($totalHadir / $hariKerja) * 100, 1) : 0;
        
        $listBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $namaBulan = $listBulan[$bulan];
        
        // Generate HTML untuk PDF
        $html = view('guru.laporan-bulanan-pdf', compact(
            'presensi', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpha',
            'hariKerja', 'persentaseKehadiran', 'namaBulan', 'tahun', 'guru', 'user'
        ))->render();
        
        // Return sebagai HTML yang bisa di-print
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="Laporan_Kehadiran_' . $namaBulan . '_' . $tahun . '.html"');
    }
}
