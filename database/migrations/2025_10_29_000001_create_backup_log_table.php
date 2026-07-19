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
        Schema::create('backup_log', function (Blueprint $table) {
            $table->id('id_backup');
            $table->string('nama_file', 255);
            $table->bigInteger('ukuran_file')->nullable();
            $table->string('lokasi_file', 500)->nullable();
            $table->unsignedBigInteger('dibuat_oleh');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('id_user')->on('users')
                  ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_log');
    }
};
