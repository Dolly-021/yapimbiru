<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Izin;
use App\Models\LaporanAktivitas;
use App\Models\EvaluasiKepsek;
use App\Models\Views\VGrafikKehadiranHarian;
use App\Models\Views\VGrafikKehadiranSiswaHarian;
use App\Models\Views\VRekapPresensiGuru;
use App\Models\Views\VRekapPresensiSiswa;
use App\Models\Kegiatan;
use App\Services\DatabaseProcedureService;
use App\Services\LogActivityService;
use App\Services\RekapPresensiService;
use Illuminate\Support\Facades\DB;

class KepalaSekolahController extends Controller
{
    protected $dbProcedure;
    protected $logActivity;
    protected $rekapPresensi;

    public function __construct(DatabaseProcedureService $dbProcedure, LogActivityService $logActivity, RekapPresensiService $rekapPresensi)
    {
        $this->dbProcedure = $dbProcedure;
        $this->logActivity = $logActivity;
        $this->rekapPresensi = $rekapPresensi;
    }

    public function dashboard()
    {
        $totalGuru = User::where('role', 'guru')->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPembina = User::where('role', 'pembina')->count();

        return view('kepala_sekolah.dashboard', compact('totalGuru', 'totalSiswa', 'totalPembina'));
    }

    public function grafikKehadiran(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $kelasId = $request->input('kelas_id');

        // Generate grafik guru dari presensi
        $grafikGuru = DB::table('presensi')
            ->select(
                'tanggal',
                DB::raw('COUNT(CASE WHEN status = "hadir" THEN 1 END) as jumlah_hadir'),
                DB::raw('COUNT(id_presensi) as total'),
                DB::raw('ROUND(COUNT(CASE WHEN status = "hadir" THEN 1 END) / COUNT(id_presensi) * 100, 2) as persentase')
            )
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Generate grafik siswa per kelas dari presensi_siswa
        $query = DB::table('presensi_siswa')
            ->join('siswa', 'presensi_siswa.id_siswa', '=', 'siswa.id_siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->select(
                'presensi_siswa.tanggal',
                'kelas.id_kelas',
                'kelas.nama_kelas',
                DB::raw('COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) as jumlah_hadir'),
                DB::raw('COUNT(presensi_siswa.id_presensi_siswa) as total'),
                DB::raw('ROUND(COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) / COUNT(presensi_siswa.id_presensi_siswa) * 100, 2) as persentase')
            )
            ->whereBetween('presensi_siswa.tanggal', [$startDate, $endDate]);

        if ($kelasId) {
            $query->where('siswa.id_kelas', $kelasId);
        }

        $grafikSiswa = $query
            ->groupBy('presensi_siswa.tanggal', 'kelas.id_kelas', 'kelas.nama_kelas')
            ->orderBy('kelas.nama_kelas')
            ->orderBy('presensi_siswa.tanggal')
            ->get();

        // Get list kelas untuk dropdown
        $daftarKelas = DB::table('kelas')->orderBy('nama_kelas')->get();

        return view('kepala_sekolah.grafik-kehadiran', compact('grafikGuru', 'grafikSiswa', 'startDate', 'endDate', 'daftarKelas', 'kelasId'));
    }

    public function rekapPresensi(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $tipe = $request->input('tipe', 'guru');

        if ($tipe === 'guru') {
            // Generate rekap guru dari presensi
            $rekap = DB::table('users')
                ->leftJoin('presensi', function($join) use ($bulan, $tahun) {
                    $join->on('users.id_user', '=', 'presensi.id_user')
                        ->whereRaw('MONTH(presensi.tanggal) = ?', [$bulan])
                        ->whereRaw('YEAR(presensi.tanggal) = ?', [$tahun]);
                })
                ->select(
                    'users.id_user',
                    'users.nama_lengkap as nama_lengkap',
                    DB::raw('COUNT(CASE WHEN presensi.status = "hadir" THEN 1 END) as total_hadir'),
                    DB::raw('COUNT(CASE WHEN presensi.status = "izin" THEN 1 END) as total_izin'),
                    DB::raw('COUNT(CASE WHEN presensi.status = "sakit" THEN 1 END) as total_sakit'),
                    DB::raw('COUNT(CASE WHEN presensi.status = "alpha" THEN 1 END) as total_alpha'),
                    DB::raw('COUNT(presensi.id_presensi) as total_hari'),
                    DB::raw('ROUND(COUNT(CASE WHEN presensi.status = "hadir" THEN 1 END) / NULLIF(COUNT(presensi.id_presensi), 0) * 100, 2) as persentase')
                )
                ->where('users.role', 'guru')
                ->groupBy('users.id_user', 'users.nama_lengkap')
                ->orderBy('users.nama_lengkap')
                ->get();
        } else {
            // Generate rekap siswa per kelas dari presensi_siswa
            $rekap = DB::table('siswa')
                ->join('users', 'siswa.id_user', '=', 'users.id_user')
                ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
                ->leftJoin('presensi_siswa', function($join) use ($bulan, $tahun) {
                    $join->on('siswa.id_siswa', '=', 'presensi_siswa.id_siswa')
                        ->whereRaw('MONTH(presensi_siswa.tanggal) = ?', [$bulan])
                        ->whereRaw('YEAR(presensi_siswa.tanggal) = ?', [$tahun]);
                })
                ->select(
                    'siswa.id_siswa',
                    'users.nama_lengkap as nama_lengkap',
                    'kelas.nama_kelas',
                    DB::raw('COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) as hadir'),
                    DB::raw('COUNT(CASE WHEN presensi_siswa.status = "izin" THEN 1 END) as izin'),
                    DB::raw('COUNT(CASE WHEN presensi_siswa.status = "sakit" THEN 1 END) as sakit'),
                    DB::raw('COUNT(CASE WHEN presensi_siswa.status = "alpha" THEN 1 END) as alfa'),
                    DB::raw('COUNT(presensi_siswa.id_presensi_siswa) as total_hari'),
                    DB::raw('ROUND(COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) / NULLIF(COUNT(presensi_siswa.id_presensi_siswa), 0) * 100, 2) as persentase')
                )
                ->groupBy('siswa.id_siswa', 'users.nama_lengkap', 'kelas.nama_kelas', 'kelas.id_kelas')
                ->orderBy('kelas.nama_kelas')
                ->orderBy('users.nama_lengkap')
                ->get();
        }

        return view('kepala_sekolah.rekap-presensi', compact('rekap', 'bulan', 'tahun', 'tipe'));
    }

    public function izin()
    {
        $izinList = Izin::with(['user.siswa.kelas', 'guru.user', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('kepala_sekolah.izin', compact('izinList'));
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

    public function laporan()
    {
        $laporan = LaporanAktivitas::with(['pembina.user', 'guru.user'])
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->paginate(20);

        return view('kepala_sekolah.laporan', compact('laporan'));
    }

    public function reviewLaporan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reviewed,approved,rejected',
            'catatan_kepsek' => 'nullable|string',
        ]);

        $laporan = LaporanAktivitas::findOrFail($id);
        $laporan->update([
            'status' => $request->status,
            'catatan_kepsek' => $request->catatan_kepsek,
            'reviewed_at' => now(),
        ]);

        $this->logActivity->logCrud('update', auth()->user()->id_user, 'laporan_aktivitas', $id);

        return redirect()->back()->with('success', 'Laporan berhasil direview');
    }

    public function createEvaluasi(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:catatan,rekomendasi,evaluasi',
            'isi_evaluasi' => 'required|string',
            'id_target_user' => 'required|exists:users,id_user',
        ]);

        $evaluasi = EvaluasiKepsek::create([
            'id_laporan' => $id,
            'id_target_user' => $request->id_target_user,
            'tipe' => $request->tipe,
            'isi_evaluasi' => $request->isi_evaluasi,
            'created_by' => auth()->user()->id_user,
        ]);

        $this->logActivity->logCrud('create', auth()->user()->id_user, 'evaluasi_kepsek', $evaluasi->id_evaluasi);

        return redirect()->back()->with('success', 'Evaluasi berhasil ditambahkan');
    }

    public function downloadRekap(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $guruId = $request->input('guru_id');

        // Generate rekap data dari tabel presensi dengan filter jika ada
        $rekapGuru = $this->rekapPresensi->generateRekapGuru($bulan, $tahun, $guruId);

        // Get list guru untuk dropdown
        $daftarGuru = User::where('role', 'guru')->orderBy('nama_lengkap')->get();

        $rekap = [
            'guru' => $rekapGuru,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return view('kepala_sekolah.download-rekap', compact('rekap', 'bulan', 'tahun', 'daftarGuru', 'guruId'));
    }

    public function kegiatan()
    {
        // Auto-update status kegiatan berdasarkan waktu
        $this->autoUpdateStatusKegiatan();
        
        $kegiatan = Kegiatan::with('pembuatKegiatan')
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(20);
        
        return view('kepala_sekolah.kegiatan.index', compact('kegiatan'));
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

    public function createKegiatan()
    {
        return view('kepala_sekolah.kegiatan.create');
    }

    public function storeKegiatan(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:rapat,ujian,acara_resmi,lainnya',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal,
                'tanggal_selesai' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'tempat' => $request->lokasi,
                'status' => 'planned',
                'dibuat_oleh' => auth()->user()->id_user,
            ]);
            
            $this->logActivity->log('create_kegiatan', auth()->user()->id_user, "Tambah kegiatan: {$kegiatan->nama_kegiatan}");
            
            DB::commit();
            return redirect()->route('kepala_sekolah.kegiatan')->with('success', 'Kegiatan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan kegiatan: ' . $e->getMessage());
        }
    }

    public function editKegiatan($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Prevent edit jika status ongoing atau completed
        if (in_array($kegiatan->status, ['ongoing', 'completed'])) {
            return redirect()->route('kepala_sekolah.kegiatan')
                ->with('error', 'Tidak dapat mengedit kegiatan yang sedang berlangsung atau sudah selesai');
        }
        
        return view('kepala_sekolah.kegiatan.edit', compact('kegiatan'));
    }

    public function updateKegiatan(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Prevent update jika status ongoing atau completed
        if (in_array($kegiatan->status, ['ongoing', 'completed'])) {
            return redirect()->route('kepala_sekolah.kegiatan')
                ->with('error', 'Tidak dapat mengubah kegiatan yang sedang berlangsung atau sudah selesai');
        }
        
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:rapat,ujian,acara_resmi,lainnya',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
        ]);

        DB::beginTransaction();
        try {
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'deskripsi' => $request->deskripsi,
                'tanggal_mulai' => $request->tanggal,
                'tanggal_selesai' => $request->tanggal,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'tempat' => $request->lokasi,
                'status' => $request->status,
            ]);
            
            $this->logActivity->log('update_kegiatan', auth()->user()->id_user, "Update kegiatan: {$kegiatan->nama_kegiatan}");
            
            DB::commit();
            return redirect()->route('kepala_sekolah.kegiatan')->with('success', 'Kegiatan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate kegiatan: ' . $e->getMessage());
        }
    }

    public function deleteKegiatan($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Prevent delete jika status ongoing atau completed
        if (in_array($kegiatan->status, ['ongoing', 'completed'])) {
            return redirect()->route('kepala_sekolah.kegiatan')
                ->with('error', 'Tidak dapat menghapus kegiatan yang sedang berlangsung atau sudah selesai');
        }
        
        DB::beginTransaction();
        try {
            $namaKegiatan = $kegiatan->nama_kegiatan;
            $kegiatan->delete();
            
            $this->logActivity->log('delete_kegiatan', auth()->user()->id_user, "Hapus kegiatan: {$namaKegiatan}");
            
            DB::commit();
            return redirect()->route('kepala_sekolah.kegiatan')->with('success', 'Kegiatan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }
}
