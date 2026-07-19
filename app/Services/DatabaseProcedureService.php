<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseProcedureService
{
    /**
     * Approve atau reject izin
     */
    public function approveIzin(int $idIzin, int $idApprover, string $status, ?string $catatan = null)
    {
        DB::statement('CALL sp_approve_izin(?, ?, ?, ?)', [
            $idIzin,
            $idApprover,
            $status,
            $catatan
        ]);
    }

    /**
     * Hapus siswa beserta relasinya
     */
    public function hapusSiswa(int $idSiswa)
    {
        DB::statement('CALL sp_hapus_siswa(?)', [$idSiswa]);
    }

    /**
     * Input presensi harian (insert atau update)
     */
    public function inputPresensiHarian(int $idUser, string $tanggal, string $jamMasuk, string $status, ?string $keterangan = null)
    {
        DB::statement('CALL sp_input_presensi_harian(?, ?, ?, ?, ?)', [
            $idUser,
            $tanggal,
            $jamMasuk,
            $status,
            $keterangan
        ]);
    }

    /**
     * Rekap presensi bulanan
     */
    public function rekapPresensiBulanan(int $bulan, int $tahun, ?string $role = null)
    {
        $result = DB::select('CALL sp_rekap_presensi_bulanan(?, ?, ?)', [
            $bulan,
            $tahun,
            $role
        ]);

        return $result;
    }

    /**
     * Rekap tugas kelas
     */
    public function rekapTugasKelas(int $idKelas)
    {
        $result = DB::select('CALL sp_rekap_tugas_kelas(?)', [$idKelas]);
        return $result;
    }
}
