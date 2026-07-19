<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Presensi;
use App\Models\PresensiSiswa;
use App\Models\Jadwal;
use App\Models\JadwalStatus;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\Kegiatan;
use App\Models\LaporanAktivitas;
use App\Models\Views\VStatistikKehadiranKelas;
use App\Models\Views\VRekapPresensiGuru;
use App\Services\DatabaseFunctionService;
use App\Services\LogActivityService;

class PembinaController extends Controller
{
    protected $dbFunction;
    protected $logActivity;

    public function __construct(DatabaseFunctionService $dbFunction, LogActivityService $logActivity)
    {
        $this->dbFunction = $dbFunction;
        $this->logActivity = $logActivity;
    }

    public function dashboard()
    {
        // Statistik kehadiran sederhana tanpa view
        $totalJadwal = Jadwal::count();
        $jadwalAktif = $totalJadwal; // Placeholder - kolom is_active tidak ada di tabel
        $totalLaporan = LaporanAktivitas::count();
        $laporanPending = LaporanAktivitas::where('status', 'submitted')->count();
        
        // Data presensi hari ini
        $presensiHariIni = Presensi::whereDate('tanggal', today())->count();
        $presensiSiswaHariIni = PresensiSiswa::whereDate('tanggal', today())->count();

        return view('pembina.dashboard', compact('totalJadwal', 'jadwalAktif', 'totalLaporan', 'laporanPending', 'presensiHariIni', 'presensiSiswaHariIni'));
    }

    public function statistikKehadiran(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $statistikKelas = VStatistikKehadiranKelas::all();

        $rekapGuru = VRekapPresensiGuru::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('pembina.statistik-kehadiran', compact('statistikKelas', 'rekapGuru', 'bulan', 'tahun'));
    }

    public function dataPresensi(Request $request)
    {
        $tipe = $request->input('tipe', 'siswa');
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        if ($tipe === 'guru') {
            $presensi = Presensi::with('user')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'desc')
                ->paginate(50);
        } else {
            $presensi = PresensiSiswa::with('siswa.user')
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->orderBy('tanggal', 'desc')
                ->paginate(50);
        }

        return view('pembina.data-presensi', compact('presensi', 'tipe', 'startDate', 'endDate'));
    }

    public function statusJadwal()
    {
        $jadwal = Jadwal::with(['kelas', 'guru.user'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $jadwalStatus = JadwalStatus::with('jadwal')
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('pembina.status-jadwal', compact('jadwal', 'jadwalStatus'));
    }

    public function reviewMateri()
    {
        $materi = Materi::with(['kelas', 'guru.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $materiCompliance = [];
        foreach ($materi as $item) {
            $materiCompliance[$item->id_materi] = $this->dbFunction->checkMateriCompliance($item->id_materi);
        }

        return view('pembina.review-materi', compact('materi', 'materiCompliance'));
    }

    public function laporan()
    {
        $laporan = LaporanAktivitas::with(['pembina.user', 'guru.user'])
            ->where('id_pembina', auth()->user()->pembina->id_pembina ?? null)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->paginate(20);

        return view('pembina.laporan', compact('laporan'));
    }

    public function detailLaporan($id)
    {
        $laporan = LaporanAktivitas::with(['pembina.user', 'guru.user', 'evaluasi'])
            ->findOrFail($id);

        if ($laporan->id_pembina !== auth()->user()->pembina->id_pembina ?? null) {
            abort(403, 'Unauthorized');
        }

        return view('pembina.detail-laporan', compact('laporan'));
    }

    public function createLaporan()
    {
        return view('pembina.create-laporan');
    }

    public function storeLaporan(Request $request)
    {
        $request->validate([
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer',
            'id_guru' => 'nullable|exists:guru,id_guru',
            'kegiatan' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $laporan = LaporanAktivitas::create([
            'id_pembina' => auth()->user()->pembina->id_pembina,
            'id_guru' => $request->id_guru,
            'periode_bulan' => $request->periode_bulan,
            'periode_tahun' => $request->periode_tahun,
            'kegiatan' => $request->kegiatan,
            'catatan' => $request->catatan,
            'status' => 'draft',
        ]);

        $this->logActivity->logCrud('create', auth()->user()->id_user, 'laporan_aktivitas', $laporan->id_laporan);

        return redirect()->route('pembina.laporan')->with('success', 'Laporan berhasil dibuat');
    }

    public function submitLaporan($id)
    {
        $laporan = LaporanAktivitas::findOrFail($id);

        if ($laporan->id_pembina !== auth()->user()->pembina->id_pembina ?? null) {
            abort(403, 'Unauthorized');
        }

        $laporan->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->logActivity->logCrud('update', auth()->user()->id_user, 'laporan_aktivitas', $id);

        return redirect()->back()->with('success', 'Laporan berhasil disubmit');
    }

    /**
     * Data Presensi per Kelas
     */
    public function dataPresensiKelas($id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
            $presensi = PresensiSiswa::whereHas('jadwal', function ($query) use ($id) {
                $query->where('id_kelas', $id);
            })->with(['siswa', 'jadwal'])
                ->orderBy('tanggal_presensi', 'desc')
                ->paginate(20);

            return view('pembina.presensi-kelas', compact('presensi', 'kelas'));
        } catch (\Exception $e) {
            \Log::error('Error in dataPresensiKelas: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data presensi kelas');
        }
    }

    /**
     * Jadwal Aktif
     */
    public function jadwalAktif(Request $request)
    {
        try {
            $query = Jadwal::with(['kelas', 'guru.user']);
            
            // Filter hari ini saja
            $hariIni = now()->locale('id')->dayName;
            $query->where('hari', $hariIni);
            
            // Filter jurusan jika ada
            if ($request->filled('jurusan')) {
                $query->whereHas('kelas', function($q) use ($request) {
                    $q->where('nama_kelas', 'like', '%-' . $request->jurusan . '-%');
                });
            }
            
            // Filter tingkat jika ada
            if ($request->filled('tingkat')) {
                $query->whereHas('kelas', function($q) use ($request) {
                    $q->where('nama_kelas', 'like', $request->tingkat . '-%');
                });
            }
            
            $jadwal = $query->orderBy('jam_mulai')->get();

            return view('pembina.jadwal', compact('jadwal', 'hariIni'));
        } catch (\Exception $e) {
            \Log::error('Error in jadwalAktif: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data jadwal');
        }
    }

    /**
     * Materi Pembelajaran - View Only
     */
    public function materiPembelajaran()
    {
        try {
            $materi = Materi::with(['guru.user', 'kelas'])
                ->orderBy('uploaded_at', 'desc')
                ->paginate(20);

            return view('pembina.materi', compact('materi'));
        } catch (\Exception $e) {
            \Log::error('Error in materiPembelajaran: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengambil data materi: ' . $e->getMessage());
        }
    }

    /**
     * Download Materi
     */
    public function downloadMateri($id)
    {
        try {
            $materi = Materi::findOrFail($id);
            
            if (!file_exists(storage_path('app/' . $materi->file_path))) {
                return back()->with('error', 'File tidak ditemukan');
            }

            return Storage::download($materi->file_path, $materi->nama_file);
        } catch (\Exception $e) {
            \Log::error('Error in downloadMateri: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengunduh file');
        }
    }

    /**
    /**
     * Laporan Aktivitas
     */
    public function laporanAktivitas()
    {
        $laporan = LaporanAktivitas::with(['pembina.user', 'guru.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pembina.laporan-aktivitas', compact('laporan'));
    }

    /**
     * Save Catatan/Rekomendasi
     */
    public function saveCatatan(Request $request)
    {
        try {
            $request->validate([
                'target_type' => 'required|in:guru,siswa,kelas',
                'target_id' => 'required|integer',
                'catatan' => 'required|string|min:10'
            ]);

            $catatan = [
                'user_id' => auth()->user()->id_user,
                'target_type' => $request->target_type,
                'target_id' => $request->target_id,
                'catatan' => $request->catatan,
                'tanggal' => now()
            ];

            // Simpan ke session atau database (sesuaikan dengan struktur yang ada)
            session()->put('catatan_' . $request->target_type . '_' . $request->target_id, $catatan);

            return back()->with('success', 'Catatan berhasil disimpan');
        } catch (\Exception $e) {
            \Log::error('Error in saveCatatan: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan catatan');
        }
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
        
        return view('pembina.kegiatan.index', compact('kegiatan', 'filter'));
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
        return view('pembina.kegiatan.detail', compact('kegiatan'));
    }
}

