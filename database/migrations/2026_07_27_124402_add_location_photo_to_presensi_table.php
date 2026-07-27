<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom GPS dan foto untuk absen masuk guru.
     */
    public function up(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            // Koordinat GPS saat absen masuk
            $table->string('latitude', 20)->nullable()->after('jam_masuk')->comment('Latitude GPS saat absen masuk');
            $table->string('longitude', 20)->nullable()->after('latitude')->comment('Longitude GPS saat absen masuk');
            // Foto bukti kehadiran saat absen masuk
            $table->string('foto_absen_masuk', 255)->nullable()->after('longitude')->comment('Foto bukti kehadiran saat absen masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'foto_absen_masuk']);
        });
    }
};
