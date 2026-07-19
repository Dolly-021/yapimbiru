<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\AbsenSiswa;
use App\Models\Guru;
use App\Models\GuruKelasMapel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AbsenController extends Controller
{
    public function index()
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        // Ambil semua kombinasi unik (kelas, mapel) yang diampu guru
        $mapels = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->with(['kelas', 'guru.user'])
            ->get()
            ->unique(function ($item) {
                // Kombinasi unik: id_kelas + mata_pelajaran
                return $item->id_kelas . '-' . $item->mata_pelajaran;
            })
            ->map(function($item) use ($guru) {
                // Hitung total absens untuk kombinasi kelas-mapel ini
                $totalAbsens = Absen::whereIn('guru_kelas_mapel_id', 
                    GuruKelasMapel::where('id_guru', $guru->id_guru)
                        ->where('id_kelas', $item->id_kelas)
                        ->where('mata_pelajaran', $item->mata_pelajaran)
                        ->pluck('id_guru_kelas_mapel')
                )->count();
                
                // Hitung total jadwal untuk kombinasi kelas-mapel ini
                $totalJadwal = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
                    ->where('id_kelas', $item->id_kelas)
                    ->where('mata_pelajaran', $item->mata_pelajaran)
                    ->count();
                
                // Tambah atribut ke model
                $item->total_absens = $totalAbsens;
                $item->jadwal_count = $totalJadwal;
                
                return $item;
            })
            ->values();
        
        return view('guru.absen.index', compact('mapels'));
    }

    public function create(Request $request)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        $mapelName = $request->query('mata_pelajaran');
        $idKelas = $request->query('id_kelas');
        
        if (!$mapelName || !$idKelas) {
            abort(400, 'Parameter mata_pelajaran dan id_kelas diperlukan');
        }
        
        // Verify guru mengajar mapel ini di kelas ini
        $jadwal = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->where('mata_pelajaran', $mapelName)
            ->where('id_kelas', $idKelas)
            ->first();
        
        if (!$jadwal) {
            abort(403, 'Anda tidak mengajar mata pelajaran ini');
        }
        
        $siswaKelas = Siswa::where('id_kelas', $idKelas)->with('user')->get();
        
        return view('guru.absen.create', compact('mapelName', 'idKelas', 'siswaKelas', 'jadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_pelajaran' => 'required|string',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jam_buka' => 'required|date_format:Y-m-d\TH:i',
            'jam_tutup' => 'required|date_format:Y-m-d\TH:i|after:jam_buka',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        // Verify guru mengajar mapel ini di kelas ini
        $jadwal = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->where('mata_pelajaran', $validated['mata_pelajaran'])
            ->where('id_kelas', $validated['id_kelas'])
            ->first();
        
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Anda tidak mengajar mata pelajaran ini');
        }
        
        // Cari atau buat GuruKelasMapel entry dengan id_tahun_ajaran dari jadwal
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        $guruKelasMapel = GuruKelasMapel::firstOrCreate(
            [
                'id_guru' => $guru->id_guru,
                'id_kelas' => $validated['id_kelas'],
                'mata_pelajaran' => $validated['mata_pelajaran']
            ],
            [
                'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran ?? $jadwal->id_tahun_ajaran ?? 1
            ]
        );
        
        $absen = Absen::create([
            'guru_id' => $guru->id_guru,
            'kelas_id' => $validated['id_kelas'],
            'guru_kelas_mapel_id' => $guruKelasMapel->id_guru_kelas_mapel,
            'jam_buka' => $validated['jam_buka'],
            'jam_tutup' => $validated['jam_tutup'],
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'buka'
        ]);

        $siswaKelas = Siswa::where('id_kelas', $validated['id_kelas'])->get();
        foreach ($siswaKelas as $siswa) {
            AbsenSiswa::create([
                'absen_id' => $absen->id,
                'id_siswa' => $siswa->id_siswa,
                'status' => 'tidak_hadir'
            ]);
        }

        return redirect()->route('guru.absen.index')->with('success', 'Absen berhasil dibuat');
    }

    public function show(Absen $absen)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        if ($absen->guru_id != $guru->id_guru) {
            abort(403);
        }

        $absenSiswas = $absen->absenSiswas()->with(['siswa.user'])->get();
        
        return view('guru.absen.show', compact('absen', 'absenSiswas'));
    }

    public function update(Request $request, Absen $absen)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        if ($absen->guru_id != $guru->id_guru) {
            abort(403);
        }

        $validated = $request->validate([
            'absen_status' => 'required|array',
            'absen_status.*' => 'required|in:hadir,izin,sakit,tidak_hadir',
        ]);

        foreach ($validated['absen_status'] as $absenSiswaId => $status) {
            $absenSiswa = AbsenSiswa::find($absenSiswaId);
            
            if ($absenSiswa && $absenSiswa->absen_id == $absen->id) {
                $absenSiswa->update([
                    'status' => $status,
                    'waktu_absen' => now()
                ]);
            }
        }

        return redirect()->route('guru.data-kehadiran')
            ->with('success', 'Absen berhasil diperbarui');
    }

    public function dataKehadiran(Request $request)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        // Ambil semua jadwal guru untuk filter
        $jadwal = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->with('kelas')
            ->get()
            ->unique(function ($item) {
                return $item->id_kelas . '-' . $item->mata_pelajaran;
            })
            ->values();
        
        // Filter tanggal jika ada
        $tanggalFilter = $request->get('tanggal');
        $mataPelajaranFilter = $request->get('mata_pelajaran');
        $kelasFilter = $request->get('kelas');
        
        // Query dasar absen guru
        $query = Absen::where('guru_id', $guru->id_guru);
        
        // Filter tanggal jika ada
        if ($tanggalFilter) {
            $query->whereDate('jam_buka', $tanggalFilter);
        }
        
        // Filter mata pelajaran jika ada
        if ($mataPelajaranFilter) {
            $query->whereHas('guruKelasMapel', function($q) use ($mataPelajaranFilter) {
                $q->where('mata_pelajaran', $mataPelajaranFilter);
            });
        }
        
        // Filter kelas jika ada
        if ($kelasFilter) {
            $query->where('kelas_id', $kelasFilter);
        }
        
        $absens = $query->with(['kelas', 'absenSiswas.siswa.user'])->orderBy('jam_buka', 'desc')->get();
        
        return view('guru.absen.data-kehadiran', compact('jadwal', 'absens', 'tanggalFilter', 'mataPelajaranFilter', 'kelasFilter'));
    }

    public function laporanPerBulanSiswa(Request $request)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        $user = $guru->user;
        
        // Ambil semua kelas yang guru ajar
        $kelas = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->unique('id_kelas')
            ->values();
        
        // Filter bulan jika ada
        $bulanFilter = $request->get('bulan', date('Y-m'));
        $kelasFilter = $request->get('kelas');
        
        // Query dasar absen guru
        $query = Absen::where('guru_id', $guru->id_guru)
            ->with(['kelas', 'absenSiswas' => function($q) {
                $q->with(['siswa' => function($sq) {
                    $sq->with('user');
                }]);
            }]);
        
        // Filter bulan (format: YYYY-MM)
        $query->whereYear('jam_buka', date('Y', strtotime($bulanFilter)))
            ->whereMonth('jam_buka', date('m', strtotime($bulanFilter)));
        
        // Filter kelas jika ada
        if ($kelasFilter) {
            $query->where('kelas_id', $kelasFilter);
        }
        
        $absens = $query->orderBy('jam_buka', 'desc')->get();
        
        // Collect semua student IDs dari absenSiswas
        $allSiswaIds = [];
        foreach ($absens as $absen) {
            foreach ($absen->absenSiswas as $absenSiswa) {
                if (!in_array($absenSiswa->id_siswa, $allSiswaIds)) {
                    $allSiswaIds[] = $absenSiswa->id_siswa;
                }
            }
        }
        
        // Load semua siswa dengan user relation
        $allSiswa = Siswa::whereIn('id_siswa', $allSiswaIds)
            ->with('user')
            ->orderBy('id_siswa')
            ->get();
        
        // Hitung statistik
        $statistik = [];
        foreach ($absens as $absen) {
            $absenSiswas = $absen->absenSiswas;
            $total = $absenSiswas->count();
            $hadir = $absenSiswas->where('status', 'hadir')->count();
            $izin = $absenSiswas->where('status', 'izin')->count();
            $sakit = $absenSiswas->where('status', 'sakit')->count();
            $tidakHadir = $absenSiswas->where('status', 'tidak_hadir')->count();
            
            $statistik[$absen->id] = [
                'total' => $total,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'tidak_hadir' => $tidakHadir,
                'persen_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0
            ];
        }
        
        return view('guru.absen.laporan-per-bulan-siswa', compact('kelas', 'absens', 'bulanFilter', 'kelasFilter', 'statistik', 'guru', 'user', 'allSiswa'));
    }

    public function destroy(Absen $absen)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        
        if ($absen->guru_id != $guru->id_guru) {
            abort(403);
        }

        $absen->delete();
        return redirect()->route('guru.absen.index')->with('success', 'Absen berhasil dihapus');
    }

    public function downloadLaporanPerBulan(Request $request)
    {
        $guru = Guru::where('id_user', auth()->user()->id_user)->first();
        $user = $guru->user;
        
        $bulanFilter = $request->get('bulan', date('Y-m'));
        $kelasFilter = $request->get('kelas');
        
        // Query dasar absen guru dengan eager loading relasi siswa
        $query = Absen::where('guru_id', $guru->id_guru)
            ->with(['kelas', 'absenSiswas' => function($q) {
                $q->with(['siswa' => function($sq) {
                    $sq->with('user');
                }]);
            }]);
        
        // Filter bulan
        $query->whereYear('jam_buka', date('Y', strtotime($bulanFilter)))
            ->whereMonth('jam_buka', date('m', strtotime($bulanFilter)));
        
        // Filter kelas jika ada
        if ($kelasFilter) {
            $query->where('kelas_id', $kelasFilter);
        }
        
        $absens = $query->orderBy('jam_buka', 'asc')->get();
        
        // Ambil semua kelas
        $kelas = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->with('kelas')
            ->get()
            ->pluck('kelas')
            ->unique('id_kelas')
            ->values();
        
        // Collect semua student IDs dari absenSiswas
        $allSiswaIds = [];
        foreach ($absens as $absen) {
            foreach ($absen->absenSiswas as $absenSiswa) {
                if (!in_array($absenSiswa->id_siswa, $allSiswaIds)) {
                    $allSiswaIds[] = $absenSiswa->id_siswa;
                }
            }
        }
        
        // Load semua siswa dengan user relation
        $allSiswa = \App\Models\Siswa::whereIn('id_siswa', $allSiswaIds)
            ->with('user')
            ->orderBy('id_siswa')
            ->get();
        
        // Hitung statistik
        $statistik = [];
        foreach ($absens as $absen) {
            $absenSiswas = $absen->absenSiswas;
            $total = $absenSiswas->count();
            $hadir = $absenSiswas->where('status', 'hadir')->count();
            $izin = $absenSiswas->where('status', 'izin')->count();
            $sakit = $absenSiswas->where('status', 'sakit')->count();
            $tidakHadir = $absenSiswas->where('status', 'tidak_hadir')->count();
            
            $statistik[$absen->id] = [
                'total' => $total,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'tidak_hadir' => $tidakHadir,
                'persen_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0
            ];
        }
        
        // Generate PDF
        $pdf = \PDF::loadView('guru.absen.pdf-kehadiran', compact('absens', 'bulanFilter', 'kelasFilter', 'statistik', 'kelas', 'guru', 'user', 'allSiswa'));
        
        $filename = 'kehadiran_' . $bulanFilter . ($kelasFilter ? '_kelas' . $kelasFilter : '') . '.pdf';
        
        return $pdf->download($filename);
    }
}
