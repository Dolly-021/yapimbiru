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
        Schema::create('evaluasi_kepsek', function (Blueprint $table) {
            $table->id('id_evaluasi');
            $table->unsignedBigInteger('id_laporan')->nullable();
            $table->unsignedBigInteger('id_target_user')->comment('Guru/Pembina yang dievaluasi');
            $table->enum('tipe', ['catatan', 'rekomendasi', 'evaluasi'])->default('catatan');
            $table->text('isi_evaluasi');
            $table->unsignedBigInteger('created_by');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan_aktivitas')->onDelete('set null');
            $table->foreign('id_target_user')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_kepsek');
    }
};
