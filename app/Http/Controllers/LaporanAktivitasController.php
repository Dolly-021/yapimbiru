<?php

namespace App\Http\Controllers;

use App\Models\LaporanAktivitas;
use App\Models\EvaluasiKepsek;
use App\Services\LogActivityService;
use Illuminate\Http\Request;

class LaporanAktivitasController extends Controller
{
    protected $logActivity;

    public function __construct(LogActivityService $logActivity)
    {
        $this->logActivity = $logActivity;
    }

    /**
     * Daftar laporan (untuk Kepala Sekolah)
     */
    public function index()
    {
        $laporan = LaporanAktivitas::with(['pembina.user', 'guru.user'])
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->paginate(20);

        return view('laporan.index', compact('laporan'));
    }

    /**
     * Submit laporan (Guru/Pembina)
     */
    public function store(Request $request)
    {
        $request->validate([
            'periode_bulan' => 'required|integer|between:1,12',
            'periode_tahun' => 'required|integer',
            'judul_laporan' => 'required|string|max:200',
            'isi_laporan' => 'required|string',
            'file_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $user = auth()->user();
        
        $data = $request->only(['periode_bulan', 'periode_tahun', 'judul_laporan', 'isi_laporan']);
        
        // Set id_pembina atau id_guru
        if ($user->role === 'pembina' && $user->pembina) {
            $data['id_pembina'] = $user->pembina->id_pembina;
        } elseif ($user->role === 'guru' && $user->guru) {
            $data['id_guru'] = $user->guru->id_guru;
        }

        // Upload PDF jika ada
        if ($request->hasFile('file_pdf')) {
            $path = $request->file('file_pdf')->store('laporan', 'public');
            $data['file_pdf'] = $path;
        }

        $data['status'] = 'submitted';

        $laporan = LaporanAktivitas::create($data);

        $this->logActivity->logCrud('create', $user->id_user, 'laporan_aktivitas', $laporan->id_laporan);

        return redirect()->back()->with('success', 'Laporan berhasil disubmit');
    }

    /**
     * Review laporan (Kepala Sekolah)
     */
    public function review(Request $request, $id)
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

    /**
     * Beri evaluasi/catatan (Kepala Sekolah)
     */
    public function addEvaluasi(Request $request, $id)
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
}
