<?php

namespace App\Http\Controllers;

use App\Models\Views\VRekapPresensiGuruStaf;
use App\Models\Views\VRekapPresensiSiswa;
use App\Models\Views\VGrafikKehadiranHarian;
use App\Models\Views\VGrafikKehadiranSiswaHarian;
use App\Models\Views\VStatistikKehadiranKelas;
use App\Services\DatabaseProcedureService;
use App\Services\DatabaseFunctionService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    protected $dbProcedure;
    protected $dbFunction;

    public function __construct(DatabaseProcedureService $dbProcedure, DatabaseFunctionService $dbFunction)
    {
        $this->dbProcedure = $dbProcedure;
        $this->dbFunction = $dbFunction;
    }

    /**
     * Input presensi harian
     */
    public function inputPresensi(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id_user',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'keterangan' => 'nullable|string',
        ]);

        $this->dbProcedure->inputPresensiHarian(
            $request->id_user,
            $request->tanggal,
            $request->jam_masuk,
            $request->status,
            $request->keterangan
        );

        return redirect()->back()->with('success', 'Presensi berhasil diinput');
    }

    /**
     * Rekap presensi bulanan
     */
    public function rekapBulanan(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        $role = $request->input('role', null);

        $rekap = $this->dbProcedure->rekapPresensiBulanan($bulan, $tahun, $role);

        return view('presensi.rekap-bulanan', compact('rekap', 'bulan', 'tahun', 'role'));
    }

    /**
     * Grafik kehadiran (untuk Kepala Sekolah)
     */
    public function grafikKehadiran(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $grafikGuru = VGrafikKehadiranHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();

        $grafikSiswa = VGrafikKehadiranSiswaHarian::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();

        return view('presensi.grafik', compact('grafikGuru', 'grafikSiswa', 'startDate', 'endDate'));
    }

    /**
     * Rekap presensi guru/staf
     */
    public function rekapGuruStaf(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $rekap = VRekapPresensiGuruStaf::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('presensi.rekap-guru-staf', compact('rekap', 'bulan', 'tahun'));
    }

    /**
     * Rekap presensi siswa per kelas
     */
    public function rekapSiswa(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $rekap = VRekapPresensiSiswa::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('presensi.rekap-siswa', compact('rekap', 'bulan', 'tahun'));
    }

    /**
     * Statistik kehadiran per kelas (untuk Guru)
     */
    public function statistikKelas(Request $request, $idKelas)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $statistik = VStatistikKehadiranKelas::where('id_kelas', $idKelas)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        return view('presensi.statistik-kelas', compact('statistik', 'bulan', 'tahun'));
    }

    /**
     * Hitung persentase kehadiran user
     */
    public function persentaseKehadiran($idUser, $bulan, $tahun)
    {
        $persentase = $this->dbFunction->hitungPersentaseKehadiran($idUser, $bulan, $tahun);

        return response()->json([
            'id_user' => $idUser,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'persentase' => $persentase,
        ]);
    }
}
