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
        // 1. Extract unique tahun_ajaran from existing kelas
        $tahunAjaranList = DB::table('kelas')
            ->select('tahun_ajaran')
            ->distinct()
            ->whereNotNull('tahun_ajaran')
            ->get();
        
        foreach ($tahunAjaranList as $item) {
            $tahunAjaran = $item->tahun_ajaran;
            
            // Parse format "2024/2025" or "2024-2025"
            $parts = preg_split('/[\/\-]/', $tahunAjaran);
            
            if (count($parts) >= 2) {
                $tahunMulai = trim($parts[0]);
                $tahunSelesai = trim($parts[1]);
                
                // Insert tahun_ajaran if not exists
                DB::table('tahun_ajaran')->insertOrIgnore([
                    'tahun_mulai' => $tahunMulai,
                    'tahun_selesai' => $tahunSelesai,
                    'semester' => 'ganjil', // Default ganjil
                    'is_active' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // 2. Set tahun ajaran terbaru sebagai aktif
        $latestTahun = DB::table('tahun_ajaran')
            ->orderBy('tahun_mulai', 'desc')
            ->first();
            
        if ($latestTahun) {
            DB::table('tahun_ajaran')
                ->where('id_tahun_ajaran', $latestTahun->id_tahun_ajaran)
                ->update(['is_active' => true]);
        }
        
        // 3. Migrate kelas to kelas_tahun_ajaran
        $kelasList = DB::table('kelas')->get();
        
        foreach ($kelasList as $kelas) {
            if ($kelas->tahun_ajaran) {
                // Parse tahun_ajaran
                $parts = preg_split('/[\/\-]/', $kelas->tahun_ajaran);
                
                if (count($parts) >= 2) {
                    $tahunMulai = trim($parts[0]);
                    $tahunSelesai = trim($parts[1]);
                    
                    // Find tahun_ajaran id
                    $tahunAjaran = DB::table('tahun_ajaran')
                        ->where('tahun_mulai', $tahunMulai)
                        ->where('tahun_selesai', $tahunSelesai)
                        ->first();
                    
                    if ($tahunAjaran) {
                        // Insert to kelas_tahun_ajaran
                        DB::table('kelas_tahun_ajaran')->insertOrIgnore([
                            'id_kelas' => $kelas->id_kelas,
                            'id_tahun_ajaran' => $tahunAjaran->id_tahun_ajaran,
                            'id_guru_wali' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
        
        // 4. Migrate jadwal_pelajaran to guru_kelas_mapel
        // Get active tahun_ajaran
        $activeTahun = DB::table('tahun_ajaran')
            ->where('is_active', true)
            ->first();
        
        if ($activeTahun) {
            $jadwalList = DB::table('jadwal_pelajaran')
                ->select('id_guru', 'id_kelas', 'mata_pelajaran')
                ->distinct()
                ->get();
            
            foreach ($jadwalList as $jadwal) {
                DB::table('guru_kelas_mapel')->insertOrIgnore([
                    'id_guru' => $jadwal->id_guru,
                    'id_kelas' => $jadwal->id_kelas,
                    'id_tahun_ajaran' => $activeTahun->id_tahun_ajaran,
                    'mata_pelajaran' => $jadwal->mata_pelajaran,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear migrated data
        DB::table('guru_kelas_mapel')->truncate();
        DB::table('kelas_tahun_ajaran')->truncate();
        DB::table('tahun_ajaran')->truncate();
    }
};
