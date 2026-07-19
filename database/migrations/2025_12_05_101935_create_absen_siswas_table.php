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
        Schema::create('absen_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('absen_id');
            $table->unsignedBigInteger('id_siswa');
            $table->enum('status', ['hadir', 'tidak_hadir', 'izin', 'sakit'])->default('tidak_hadir');
            $table->dateTime('waktu_absen')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('absen_id')->references('id')->on('absens')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');

            // Unique constraint
            $table->unique(['absen_id', 'id_siswa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen_siswas');
    }
};
