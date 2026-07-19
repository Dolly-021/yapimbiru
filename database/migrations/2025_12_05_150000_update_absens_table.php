<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if absens table exists and has the right structure
        if (Schema::hasTable('absens')) {
            Schema::table('absens', function (Blueprint $table) {
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('absens', 'id_guru')) {
                    $table->unsignedBigInteger('id_guru')->after('id');
                }
                if (!Schema::hasColumn('absens', 'id_kelas')) {
                    $table->unsignedBigInteger('id_kelas')->after('id_guru');
                }
                if (!Schema::hasColumn('absens', 'guru_kelas_mapel_id')) {
                    $table->unsignedBigInteger('guru_kelas_mapel_id')->after('id_kelas');
                }
                if (!Schema::hasColumn('absens', 'jam_buka')) {
                    $table->dateTime('jam_buka')->after('guru_kelas_mapel_id');
                }
                if (!Schema::hasColumn('absens', 'jam_tutup')) {
                    $table->dateTime('jam_tutup')->after('jam_buka');
                }
                if (!Schema::hasColumn('absens', 'status')) {
                    $table->enum('status', ['buka', 'tutup'])->default('buka')->after('jam_tutup');
                }
                if (!Schema::hasColumn('absens', 'keterangan')) {
                    $table->text('keterangan')->nullable()->after('status');
                }
            });
        } else {
            // Create absens table if it doesn't exist
            Schema::create('absens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_guru');
                $table->unsignedBigInteger('id_kelas');
                $table->unsignedBigInteger('guru_kelas_mapel_id');
                $table->dateTime('jam_buka');
                $table->dateTime('jam_tutup');
                $table->enum('status', ['buka', 'tutup'])->default('buka');
                $table->text('keterangan')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
                $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
                $table->foreign('guru_kelas_mapel_id')->references('id_guru_kelas_mapel')->on('guru_kelas_mapel')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table, just remove the columns we added
        if (Schema::hasTable('absens')) {
            Schema::table('absens', function (Blueprint $table) {
                // Drop foreign keys if they exist
                try {
                    $table->dropForeign(['id_guru']);
                } catch (\Exception $e) {}
                try {
                    $table->dropForeign(['id_kelas']);
                } catch (\Exception $e) {}
                try {
                    $table->dropForeign(['guru_kelas_mapel_id']);
                } catch (\Exception $e) {}
            });
        }
    }
};
