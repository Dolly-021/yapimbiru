<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create view untuk rekap presensi guru dan staf
        DB::statement("DROP TABLE IF EXISTS v_rekap_presensi_guru_staf");
        
        DB::statement("
            CREATE OR REPLACE VIEW v_rekap_presensi_guru_staf AS
            SELECT 
                0 as id_rekap,
                MONTH(NOW()) as bulan,
                YEAR(NOW()) as tahun,
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
        DB::statement("DROP VIEW IF EXISTS v_rekap_presensi_guru_staf");
    }
};
