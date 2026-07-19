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
        // Modify id_guru column to be nullable or have default
        if (Schema::hasTable('absens')) {
            Schema::table('absens', function (Blueprint $table) {
                // Change id_guru to nullable if it's not already
                try {
                    DB::statement('ALTER TABLE absens MODIFY COLUMN id_guru bigint unsigned NULL');
                } catch (\Exception $e) {
                    // Column might already be nullable
                }
                
                // Change id_kelas to nullable if it's not already  
                try {
                    DB::statement('ALTER TABLE absens MODIFY COLUMN id_kelas bigint unsigned NULL');
                } catch (\Exception $e) {
                    // Column might already be nullable
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
