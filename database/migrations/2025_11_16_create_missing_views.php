<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop placeholder tables if they exist from import
        DB::statement('DROP TABLE IF EXISTS v_grafik_kehadiran_siswa_harian');
        DB::statement('DROP TABLE IF EXISTS v_rekap_presensi_siswa');

        // Create v_grafik_kehadiran_siswa_harian view
        DB::statement('
            CREATE OR REPLACE VIEW v_grafik_kehadiran_siswa_harian AS
            SELECT 
                ps.tanggal,
                COUNT(CASE WHEN ps.status = "hadir" THEN 1 END) AS jumlah_hadir,
                COUNT(*) AS total_siswa,
                ROUND((COUNT(CASE WHEN ps.status = "hadir" THEN 1 END) / COUNT(*)) * 100, 2) AS persentase
            FROM presensi_siswa ps
            WHERE ps.tanggal >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY ps.tanggal
            ORDER BY ps.tanggal ASC
        ');

        // Create v_rekap_presensi_siswa view
        DB::statement('
            CREATE OR REPLACE VIEW v_rekap_presensi_siswa AS
            SELECT 
                ps.id_siswa,
                YEAR(ps.tanggal) AS tahun,
                MONTH(ps.tanggal) AS bulan,
                u.username AS nama,
                k.nama_kelas,
                COUNT(CASE WHEN ps.status = "hadir" THEN 1 END) AS hadir,
                COUNT(CASE WHEN ps.status = "izin" THEN 1 END) AS izin,
                COUNT(CASE WHEN ps.status = "sakit" THEN 1 END) AS sakit,
                COUNT(CASE WHEN ps.status = "alpha" THEN 1 END) AS alfa,
                COUNT(*) AS total_hari,
                ROUND((COUNT(CASE WHEN ps.status = "hadir" THEN 1 END) / COUNT(*)) * 100, 2) AS persentase
            FROM presensi_siswa ps
            LEFT JOIN siswa s ON ps.id_siswa = s.id_siswa
            LEFT JOIN users u ON s.id_user = u.id_user
            LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
            WHERE YEAR(ps.tanggal) >= YEAR(CURDATE()) - 1
            GROUP BY ps.id_siswa, YEAR(ps.tanggal), MONTH(ps.tanggal), u.username, k.nama_kelas
            ORDER BY tahun DESC, bulan DESC
        ');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_grafik_kehadiran_siswa_harian');
        DB::statement('DROP VIEW IF EXISTS v_rekap_presensi_siswa');
    }
};
