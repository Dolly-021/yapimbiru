<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presensi_siswa', function (Blueprint $table) {
            // Tambah column untuk tracking verifikasi
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending')->after('keterangan');
            $table->unsignedBigInteger('di_verifikasi_oleh')->nullable()->after('status_verifikasi');
            $table->text('alasan_reject')->nullable()->after('di_verifikasi_oleh');
            $table->timestamp('diverifikasi_at')->nullable()->after('alasan_reject');
            $table->enum('diinput_oleh_tipe', ['siswa', 'guru'])->default('guru')->after('diverifikasi_at');
            
            // Add index untuk query cepat
            $table->index('status_verifikasi');
            $table->index(['status_verifikasi', 'id_jadwal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_siswa', function (Blueprint $table) {
            $table->dropIndex(['status_verifikasi', 'id_jadwal']);
            $table->dropIndex(['status_verifikasi']);
            $table->dropColumn(['status_verifikasi', 'di_verifikasi_oleh', 'alasan_reject', 'diverifikasi_at', 'diinput_oleh_tipe']);
        });
    }
};
