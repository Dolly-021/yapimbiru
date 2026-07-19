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
        Schema::create('guru_kelas_mapel', function (Blueprint $table) {
            $table->id('id_guru_kelas_mapel');
            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_tahun_ajaran');
            $table->string('mata_pelajaran', 100);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_tahun_ajaran')->references('id_tahun_ajaran')->on('tahun_ajaran')->onDelete('cascade');
            
            // Unique constraint: satu guru hanya mengajar satu mapel di satu kelas per tahun ajaran
            $table->unique(['id_guru', 'id_kelas', 'id_tahun_ajaran', 'mata_pelajaran'], 'unique_guru_kelas_mapel_tahun');
            
            // Index untuk query
            $table->index('id_guru');
            $table->index(['id_kelas', 'id_tahun_ajaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_kelas_mapel');
    }
};
