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
        Schema::create('kelas_tahun_ajaran', function (Blueprint $table) {
            $table->id('id_kelas_tahun_ajaran');
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->unsignedBigInteger('id_guru_wali')->nullable(); // Wali kelas
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_tahun_ajaran')->references('id_tahun_ajaran')->on('tahun_ajaran')->onDelete('cascade');
            $table->foreign('id_guru_wali')->references('id_guru')->on('guru')->onDelete('set null');
            
            // Unique constraint: satu kelas hanya bisa ada sekali per tahun ajaran
            $table->unique(['id_kelas', 'id_tahun_ajaran']);
            
            // Index untuk query
            $table->index('id_tahun_ajaran');
            $table->index('id_guru_wali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_tahun_ajaran');
    }
};
