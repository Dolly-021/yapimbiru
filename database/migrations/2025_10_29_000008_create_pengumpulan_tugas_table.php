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
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id('id_pengumpulan');
            $table->unsignedBigInteger('id_tugas');
            $table->unsignedBigInteger('id_siswa');
            $table->string('file_jawaban', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->dateTime('waktu_submit');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('feedback_guru')->nullable();
            $table->enum('status', ['terlambat', 'tepat_waktu'])->default('tepat_waktu');
            
            $table->unique(['id_tugas', 'id_siswa']);
            $table->foreign('id_tugas')->references('id_tugas')->on('tugas')->onDelete('cascade');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');
            $table->index('waktu_submit');
            $table->index(['status', 'nilai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
