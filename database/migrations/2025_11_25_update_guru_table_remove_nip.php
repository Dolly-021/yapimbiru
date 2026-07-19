<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            // Tambah kolom baru jika belum ada
            if (!Schema::hasColumn('guru', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 1)->nullable();
            }
            if (!Schema::hasColumn('guru', 'agama')) {
                $table->string('agama', 50)->nullable();
            }
            if (!Schema::hasColumn('guru', 'no_hp')) {
                $table->string('no_hp', 20)->nullable();
            }
        });
        
        // Ubah NIP menjadi nullable dengan raw SQL untuk menghindari error
        DB::statement('ALTER TABLE guru MODIFY nip VARCHAR(30) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            if (Schema::hasColumn('guru', 'jenis_kelamin')) {
                $table->dropColumn('jenis_kelamin');
            }
            if (Schema::hasColumn('guru', 'agama')) {
                $table->dropColumn('agama');
            }
            if (Schema::hasColumn('guru', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });
        
        // Kembalikan NIP menjadi NOT NULL
        DB::statement('ALTER TABLE guru MODIFY nip VARCHAR(30) NOT NULL');
    }
};
