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
        // Kolom status_aktif sudah ada di tabel users
        // Kita hanya perlu memastikan logic nya benar
        // status_aktif = 0 untuk pending verification
        // status_aktif = 1 untuk verified/active
        
        // Update existing guru to be verified
        DB::table('users')
            ->where('role', 'guru')
            ->update(['status_aktif' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes needed as we're using existing column
    }
};
