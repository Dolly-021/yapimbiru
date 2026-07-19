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
        Schema::create('data_siswa_master', function (Blueprint $table) {
            $table->id('id_siswa_master');
            $table->string('nis', 20)->unique();
            $table->string('nama_siswa', 255);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->string('agama', 50)->nullable();
            $table->string('sekolah_asal', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->unsignedBigInteger('id_kelas');
            $table->string('nama_kelas', 50);
            $table->boolean('is_registered')->default(false); // Flag apakah sudah register
            $table->timestamps();

            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->index('nis');
            $table->index('nama_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_siswa_master');
    }
};
