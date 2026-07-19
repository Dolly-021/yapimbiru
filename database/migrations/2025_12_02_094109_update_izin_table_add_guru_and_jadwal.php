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
        Schema::table('izin', function (Blueprint $table) {
            // Drop approval columns only if they exist
            if (Schema::hasColumn('izin', 'status_approval')) {
                $table->dropColumn('status_approval');
            }
            if (Schema::hasColumn('izin', 'disetujui_oleh')) {
                $table->dropColumn('disetujui_oleh');
            }
            if (Schema::hasColumn('izin', 'catatan_approval')) {
                $table->dropColumn('catatan_approval');
            }
            if (Schema::hasColumn('izin', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            
            // Add id_guru and id_jadwal columns only if they don't exist
            if (!Schema::hasColumn('izin', 'id_guru')) {
                $table->unsignedBigInteger('id_guru')->after('id_user');
                $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('izin', 'id_jadwal')) {
                $table->unsignedBigInteger('id_jadwal')->nullable()->after('id_guru');
                $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_pelajaran')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin', function (Blueprint $table) {
            $table->dropForeign(['id_guru']);
            $table->dropForeign(['id_jadwal']);
            $table->dropColumn(['id_guru', 'id_jadwal']);
            
            // Restore approval columns
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->foreign('disetujui_oleh')->references('id_user')->on('users')->onDelete('set null');
        });
    }
};
