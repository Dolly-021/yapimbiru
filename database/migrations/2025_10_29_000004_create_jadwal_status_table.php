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
        Schema::create('jadwal_status', function (Blueprint $table) {
            $table->id('id_jadwal_status');
            $table->unsignedBigInteger('id_jadwal');
            $table->unsignedBigInteger('id_guru');
            $table->date('tanggal');
            $table->enum('status_pertemuan', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->comment('Pembina yang update');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['id_jadwal', 'tanggal'], 'unique_jadwal_tanggal');
            
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_pelajaran')->onDelete('cascade');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            $table->foreign('updated_by')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_status');
    }
};
