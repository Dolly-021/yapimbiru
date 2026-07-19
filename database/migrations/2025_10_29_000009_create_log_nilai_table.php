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
        Schema::create('log_nilai', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_pengumpulan');
            $table->unsignedBigInteger('id_siswa');
            $table->decimal('nilai_lama', 5, 2)->nullable();
            $table->decimal('nilai_baru', 5, 2);
            $table->timestamp('diubah_pada')->useCurrent();
            
            $table->foreign('id_pengumpulan')->references('id_pengumpulan')->on('pengumpulan_tugas')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->index('diubah_pada');
            $table->index('id_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_nilai');
    }
};
