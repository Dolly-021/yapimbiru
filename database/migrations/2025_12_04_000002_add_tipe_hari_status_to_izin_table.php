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
        Schema::table('izin', function (Blueprint $table) {
            if (!Schema::hasColumn('izin', 'tipe')) {
                $table->enum('tipe', ['sakit', 'izin'])->after('id_user')->default('izin');
            }
            if (!Schema::hasColumn('izin', 'hari')) {
                $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])->after('tanggal');
            }
            if (!Schema::hasColumn('izin', 'status')) {
                $table->enum('status', ['pending', 'disetujui', 'ditolak'])->after('alasan')->default('pending');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->dropColumn(['tipe', 'hari', 'status']);
        });
    }
};
