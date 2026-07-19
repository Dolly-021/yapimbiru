<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data guru yang sudah ada
        // 1. Jika guru belum punya email, generate dari NIP
        // 2. Jika sudah punya email di user, gunakan itu
        // 3. Isi jenis_kelamin, agama dari user lama jika ada
        
        // Check kolom dulu sebelum query untuk mencegah error jika kolom belum ada
        if (Schema::hasColumn('guru', 'jenis_kelamin')) {
            $gurus = DB::table('guru')->whereNull('jenis_kelamin')->get();
            
            foreach ($gurus as $guru) {
                $user = DB::table('users')->where('id_user', $guru->id_user)->first();
                
                if ($user) {
                    // Update guru dengan data dari user jika ada
                    DB::table('guru')
                        ->where('id_guru', $guru->id_guru)
                        ->update([
                            'no_hp' => $user->no_hp ?? $guru->no_hp,
                            // jenis_kelamin dan agama tetap null jika user lama tidak punya
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback, data sudah migrate
    }
};
