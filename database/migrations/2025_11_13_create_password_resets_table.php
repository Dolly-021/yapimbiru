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
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // NIS/NIP/Email
            $table->string('no_hp'); // Nomor HP untuk kirim OTP
            $table->string('otp', 6); // Kode OTP
            $table->string('token')->unique(); // Token untuk reset
            $table->enum('status', ['pending', 'verified', 'completed'])->default('pending');
            $table->integer('attempts')->default(0); // Jumlah attempt OTP
            $table->timestamp('otp_expires_at')->nullable(); // Kapan OTP expired
            $table->timestamp('verified_at')->nullable(); // Kapan OTP diverifikasi
            $table->timestamps();
            
            $table->index('identifier');
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
