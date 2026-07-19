<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\AbsenSiswa;
use App\Models\GuruKelasMapel;
use App\Models\Jadwal;
use App\Models\Siswa;
use Illuminate\Http\Request;

class AbsenController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('id_user', auth()->user()->id_user)->first();
        
        // Get ALL unique mapel from Jadwal (Senin-Jumat)
        // Group by mata_pelajaran dan id_guru untuk get unique combinations
        $mapels = Jadwal::where('id_kelas', $siswa->id_kelas)
            ->with(['guru', 'kelas'])
            ->get()
            ->groupBy('mata_pelajaran')
            ->map(function($group) {
                return $group->first(); // Take first entry from each mapel
            })
            ->values();
        
        return view('siswa.absen.index', compact('mapels', 'siswa'));
    }

    public function show($mapelName)
    {
        $siswa = Siswa::where('id_user', auth()->user()->id_user)->first();
        
        // Get first Jadwal entry for this mapel name (to show jadwal info)
        $jadwal = Jadwal::where('id_kelas', $siswa->id_kelas)
            ->where('mata_pelajaran', $mapelName)
            ->with(['guru.user', 'kelas'])
            ->first();

        // Get all absens for this mapel from all guru_kelas_mapel entries with same mata_pelajaran
        $absens = Absen::whereIn('guru_kelas_mapel_id', 
            GuruKelasMapel::where('id_kelas', $siswa->id_kelas)
                ->where('mata_pelajaran', $mapelName)
                ->pluck('id_guru_kelas_mapel')
        )
        ->with(['guru.user', 'kelas', 'absenSiswas'])
        ->orderBy('id', 'asc')
        ->get();
        
        if ($absens->isEmpty() && !$jadwal) {
            abort(404, 'Mata pelajaran tidak ditemukan');
        }
        
        // Fallback: ambil guru dari first absen jika jadwal tidak ada
        $guruName = $jadwal?->guru?->user?->nama_lengkap ?? $absens->first()?->guru?->user?->nama_lengkap ?? 'N/A';
        
        return view('siswa.absen.show', compact('jadwal', 'absens', 'siswa', 'mapelName', 'guruName'));
    }

    public function create($absenId)
    {
        $siswa = Siswa::where('id_user', auth()->user()->id_user)->first();
        $absen = Absen::with(['guru.user', 'guruKelasMapel'])->findOrFail($absenId);
        
        // Check kelas dari kelas_id (gunakan kelas_id bukan id_kelas karena itu nama field di tabel)
        if ($absen->kelas_id != $siswa->id_kelas) {
            abort(403);
        }

        // Cek apakah absen masih dibuka
        $now = now();
        if ($now < $absen->jam_buka || $now > $absen->jam_tutup) {
            return redirect()->back()->with('error', 'Waktu absen sudah tutup');
        }

        $absenSiswa = AbsenSiswa::where('absen_id', $absen->id)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();
        
        if (!$absenSiswa) {
            abort(404);
        }

        return view('siswa.absen.create', compact('absen', 'absenSiswa'));
    }

    public function store(Request $request)
    {
        \Log::info('Absen store request', [
            'method' => $request->method(),
            'wants_json' => $request->wantsJson(),
            'user' => auth()->user()?->id_user ?? null,
            'body' => $request->all()
        ]);

        $validated = $request->validate([
            'absen_id' => 'required|exists:absens,id',
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit',
            'keterangan' => 'nullable|string'
        ]);

        $siswa = Siswa::where('id_user', auth()->user()->id_user)->first();
        $absen = Absen::findOrFail($validated['absen_id']);
        
        \Log::info('Found siswa and absen', [
            'siswa_id' => $siswa->id_siswa,
            'siswa_kelas' => $siswa->id_kelas,
            'absen_kelas' => $absen->kelas_id,
        ]);

        // Check kelas dari kelas_id
        if ($absen->kelas_id != $siswa->id_kelas) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
            abort(403);
        }

        $absenSiswa = AbsenSiswa::where('absen_id', $absen->id)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();
        
        \Log::info('Found absenSiswa', [
            'absen_siswa_id' => $absenSiswa->id ?? null,
            'status_before' => $absenSiswa->status ?? null,
        ]);

        if (!$absenSiswa) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Data absen tidak ditemukan'], 404);
            }
            abort(404);
        }

        $updated = $absenSiswa->update([
            'status' => $validated['status'],
            'waktu_absen' => now(),
            'keterangan' => $validated['keterangan'] ?? null
        ]);

        \Log::info('Update result', [
            'updated' => $updated,
            'status_after' => $absenSiswa->refresh()->status,
        ]);

        // Return JSON untuk AJAX request
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Absen berhasil dicatat sebagai ' . ucfirst($validated['status'])
            ]);
        }

        return redirect()->route('siswa.absen.show', $absen->guru_kelas_mapel_id)
            ->with('success', 'Absen berhasil disimpan');
    }
}
