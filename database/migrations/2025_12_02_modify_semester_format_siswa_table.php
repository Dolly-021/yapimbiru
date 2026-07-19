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
        Schema::table('siswa', function (Blueprint $table) {
            // Ubah column semester dari tinyInteger menjadi string
            $table->string('semester', 50)->nullable()->change()->comment('Format: "X Semester Ganjil 2023/2024"');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->tinyInteger('semester')->nullable()->change();
        });
    }
};
