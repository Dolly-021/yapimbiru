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
        Schema::create('laporan_aktivitas', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_pembina')->nullable();
            $table->unsignedBigInteger('id_guru')->nullable();
            $table->tinyInteger('periode_bulan');
            $table->smallInteger('periode_tahun');
            $table->string('judul_laporan', 200);
            $table->text('isi_laporan');
            $table->string('file_pdf', 255)->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_kepsek')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_pembina')->references('id_pembina')->on('pembina')->onDelete('cascade');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_aktivitas');
    }
};
