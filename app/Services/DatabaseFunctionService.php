<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseFunctionService
{
    /**
     * Cek status keterlambatan pengumpulan tugas
     */
    public function cekKeterlambatan(string $waktuSubmit, string $deadline): string
    {
        $result = DB::selectOne('SELECT cek_keterlambatan(?, ?) as status', [
            $waktuSubmit,
            $deadline
        ]);

        return $result->status ?? 'tepat_waktu';
    }

    /**
     * Hitung persentase kehadiran
     */
    public function hitungPersentaseKehadiran(int $idUser, int $bulan, int $tahun): float
    {
        $result = DB::selectOne('SELECT hitung_persentase_kehadiran(?, ?, ?) as persentase', [
            $idUser,
            $bulan,
            $tahun
        ]);

        return (float) ($result->persentase ?? 0);
    }

    /**
     * Hitung rata-rata nilai siswa
     */
    public function hitungRataNilai(int $idSiswa, int $idKelas): float
    {
        $result = DB::selectOne('SELECT hitung_rata_nilai(?, ?) as rata_nilai', [
            $idSiswa,
            $idKelas
        ]);

        return (float) ($result->rata_nilai ?? 0);
    }

    /**
     * Check materi compliance
     */
    public function checkMateriCompliance(int $idMateri): string
    {
        $result = DB::selectOne('SELECT check_materi_compliance(?) as status', [$idMateri]);
        return $result->status ?? 'not_compliant';
    }
}
