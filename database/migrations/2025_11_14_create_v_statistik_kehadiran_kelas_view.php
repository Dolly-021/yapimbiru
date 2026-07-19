<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create simple view untuk statistik kehadiran per kelas
        // View ini hanya menampilkan data kosong terlebih dahulu untuk placeholder
        DB::statement("
            CREATE OR REPLACE VIEW v_statistik_kehadiran_kelas AS
            SELECT 
                0 as id_kelas,
                'Placeholder' as nama_kelas,
                MONTH(NOW()) as bulan,
                YEAR(NOW()) as tahun,
                0 as total_siswa_presensi,
                0 as total_pertemuan,
                0 as total_hadir,
                0 as total_izin,
                0 as total_sakit,
                0 as total_alfa,
                0 as persentase_kehadiran
            LIMIT 0
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_statistik_kehadiran_kelas");
    }
};
