<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom jam_keluar jika belum ada
        if (!Schema::hasColumn('presensi', 'jam_keluar')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->time('jam_keluar')->nullable()->after('jam_masuk')->comment('Jam keluar/pulang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('presensi', 'jam_keluar')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->dropColumn('jam_keluar');
            });
        }
    }
};
