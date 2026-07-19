<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;

class ImportJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:import {--force : Force import even if jadwal already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import jadwal pelajaran dari file CSV roster (HANYA SEKALI)';

    /**
     * Mapping kode guru ke username
     */
    private $guruMapping = [
        'SWF' => 'sriwati.barus',      // Sriwati Barus - Agama
        'AP' => 'addy.pinem',          // Addy Suranta Pinem
        'JU' => 'junellia.barus',      // Junellia Tianta Barus
        'IK' => 'ika.kurniawan',       // Ika Kurniawan
        'AL' => 'agustinus.laia',      // Agustinus Laia
        'AS' => 'angga.agnesta',       // Angga Agnesta
        'DA' => 'david.sagala',        // David Saputra Woffelson Sagala
        'IIN' => 'iin.purwanti',       // Iin Purwanti
        'DN' => 'dwi.sari',            // Dwi Nopita Sari
        'CN' => 'chandra.nainggolan',  // Chandra Nainggolan
        'IM' => 'imelia.sinuhaji',     // Imelia Rosa Sinuhaji
        'MA' => 'meliyani.barus',      // Meliyani Ajelina Barus
        'DS' => 'dewi.sembiring',      // Dewi Sutriani Sembiring
        'HA' => 'hafiz.irsyad',        // Hafiz Al Irsyad
        'NA' => 'novita.anggraini',    // Novita Anggraini
        'RR' => 'rifka.siregar',       // Rifka Rahayu Siregar
        'OC' => 'corsalina.simamora',  // Corsalina Simamora
        'EE' => 'eka.barus',           // Eka Elisata Barus
        'MI' => 'miranti.sembiring',   // Miranti Sembiring
        'MH' => 'milia.hutajulu',      // Milia Friska Rani Hutajulu
        'KB' => 'khairul.bariyah',     // Khairul Bariyah
        'ET' => 'edison.tumanggor',    // Edison J.P Tumanggor
        'LM' => 'lotar.sinaga',        // Lotar Mateus Sinaga
        'WF' => 'warnaita.barus',      // Warnaita Barus
        'HG' => 'hotmaida.ginting',    // Hotmaida Ginting
        'SRF' => 'siti.fatimah',       // Siti Fatimah
        'HM' => 'himru.batu',          // Himru Lumban Batu
        'BN' => 'benny.naibaho',       // Benny Pinondang Naibaho
        'TS' => 'togar.sihotang',      // Togar Sihotang
        'SM' => 'santun.manurung',     // Santun Manurung
        'DP' => 'dodi.perangiangin',   // Dodi Pratama Perangi-Angin
        'JS' => 'johanes.simatupang',  // Johanes Hasudungan Simatupang
        'JH' => 'jhonson.gultom',      // Jhonson Costantin Gultom
        'MS' => 'marisnauli.situmorang', // Marisnauli Situmorang
        'QL' => null,                  // TKA QL - Unknown
        'RF' => null,                  // RF - Unknown
    ];

    /**
     * Mapping nama kelas dari CSV ke database
     */
    private $kelasMapping = [
        'X TKJ 1' => 'X-TJKT-1',
        'X TKJ 2' => 'X-TJKT-2',
        'X TKJ 3' => 'X-TJKT-3',
        'X TKJ 4' => 'X-TJKT-4',
        'XI TKJ 1' => 'XI-TKJ-1',
        'XI TKJ 2' => 'XI-TKJ-2',
        'XI TKJ 3' => 'XI-TKJ-3',
        'XI TKJ 4' => 'XI-TKJ-4',
        'XII TKJ 1' => 'XII-TKJ-1',
        'XII TKJ 2' => 'XII-TKJ-2',
        'XII TKJ 3' => 'XII-TKJ-3',
        'XII TKJ 4' => 'XII-TKJ-4',
        'X TKR 1' => 'X-TO-1',
        'X TKR 2' => 'X-TO-2',
        'X TKR 3' => 'X-TO-3',
        'X TKR 4' => 'X-TO-4',
        'X TKR 5' => 'X-TO-5',
        'XI TKR 1' => 'XI-TKR-1',
        'XI TKR 2' => 'XI-TKR-2',
        'XI TKR 3' => 'XI-TKR-3',
        'XI TKR 4' => 'XI-TKR-4',
        'XI TKR 5' => 'XI-TKR-5',
        'XII TKRO 1' => 'XII-TKR-1',
        'XII TKRO 2' => 'XII-TKR-2',
        'XII TKRO 3' => 'XII-TKR-3',
        'XII TKRO 4' => 'XII-TKR-4',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if jadwal already imported
        $existingJadwal = Jadwal::count();
        
        if ($existingJadwal > 0 && !$this->option('force')) {
            $this->error('❌ Jadwal sudah pernah di-import!');
            $this->info("   Total jadwal: {$existingJadwal}");
            $this->warn('   Gunakan --force jika tetap ingin import ulang (akan hapus semua jadwal lama)');
            return 1;
        }

        if ($this->option('force')) {
            if (!$this->confirm('⚠️  Yakin ingin menghapus semua jadwal dan import ulang?', false)) {
                $this->info('Import dibatalkan.');
                return 0;
            }
            
            $this->warn('Menghapus semua jadwal lama...');
            Jadwal::truncate();
        }

        $this->info('🚀 Memulai import jadwal pelajaran...');
        
        // Load guru mapping
        $this->info('📋 Loading data guru...');
        $guruCache = $this->loadGuruCache();
        
        // Load kelas mapping
        $this->info('📋 Loading data kelas...');
        $kelasCache = $this->loadKelasCache();
        
        // Import TKJ
        $this->info('📚 Importing jadwal TKJ...');
        $countTKJ = $this->importRoster(
            base_path('roster_tkj.csv'),
            'TKJ',
            $guruCache,
            $kelasCache
        );
        
        // Import TKR
        $this->info('📚 Importing jadwal TKR...');
        $countTKR = $this->importRoster(
            base_path('roster_tkr.csv'),
            'TKR',
            $guruCache,
            $kelasCache
        );
        
        $total = $countTKJ + $countTKR;
        
        $this->newLine();
        $this->info("✅ Import selesai!");
        $this->info("   - TKJ: {$countTKJ} jadwal");
        $this->info("   - TKR: {$countTKR} jadwal");
        $this->info("   - Total: {$total} jadwal");
        
        return 0;
    }

    /**
     * Load guru cache
     */
    private function loadGuruCache()
    {
        $cache = [];
        
        $gurus = DB::table('users')
            ->join('guru', 'users.id_user', '=', 'guru.id_user')
            ->where('users.role', 'guru')
            ->select('users.username', 'guru.id_guru')
            ->get();
        
        foreach ($gurus as $guru) {
            $cache[$guru->username] = $guru->id_guru;
        }
        
        return $cache;
    }

    /**
     * Load kelas cache
     */
    private function loadKelasCache()
    {
        $cache = [];
        
        $kelasList = Kelas::all();
        
        foreach ($kelasList as $kelas) {
            $cache[$kelas->nama_kelas] = $kelas->id_kelas;
        }
        
        return $cache;
    }

    /**
     * Import roster from CSV
     */
    private function importRoster($filePath, $type, $guruCache, $kelasCache)
    {
        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 0;
        }

        $csv = array_map('str_getcsv', file($filePath));
        $count = 0;
        
        // Parse header untuk mendapatkan kolom kelas
        $kelasColumns = $this->parseKelasColumns($csv, $type);
        
        // Parse jadwal per hari
        $currentHari = null;
        $jamMappings = [];
        
        foreach ($csv as $rowIndex => $row) {
            if ($rowIndex < 2) continue; // Skip header rows
            
            // Detect hari
            if (!empty($row[0]) && in_array(strtoupper($row[0]), ['SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'])) {
                $currentHari = ucfirst(strtolower($row[0]));
                continue;
            }
            
            // Skip if no hari detected or is separator
            if (!$currentHari || empty($row[1])) continue;
            
            // Get jam
            $jamInfo = $this->parseJam($row[3]); // Column WAKTU
            if (!$jamInfo) continue;
            
            // Parse each kelas column
            foreach ($kelasColumns as $colIndex => $kelasName) {
                $mapel = trim($row[$colIndex] ?? '');
                $kodeGuru = trim($row[$colIndex + 1] ?? '');
                
                // Skip if empty or special cases
                if (empty($mapel) || $mapel == 'U P A C A R A' || $mapel == 'I S T I R A H A T' || $mapel == 'S E N A M' || $mapel == 'TKA') {
                    continue;
                }
                
                // Get guru id
                $idGuru = null;
                if (!empty($kodeGuru) && isset($this->guruMapping[$kodeGuru])) {
                    $username = $this->guruMapping[$kodeGuru];
                    if ($username && isset($guruCache[$username])) {
                        $idGuru = $guruCache[$username];
                    }
                }
                
                // Skip if guru not found
                if (!$idGuru) continue;
                
                // Get kelas id
                $dbKelasName = $this->kelasMapping[$kelasName] ?? null;
                if (!$dbKelasName || !isset($kelasCache[$dbKelasName])) {
                    continue;
                }
                
                $idKelas = $kelasCache[$dbKelasName];
                
                // Insert jadwal
                try {
                    Jadwal::create([
                        'id_kelas' => $idKelas,
                        'id_guru' => $idGuru,
                        'mata_pelajaran' => $mapel,
                        'hari' => $currentHari,
                        'jam_mulai' => $jamInfo['mulai'],
                        'jam_selesai' => $jamInfo['selesai'],
                    ]);
                    
                    $count++;
                } catch (\Exception $e) {
                    // Skip duplicates or errors
                }
            }
        }
        
        return $count;
    }

    /**
     * Parse kelas columns from header
     */
    private function parseKelasColumns($csv, $type)
    {
        $kelasColumns = [];
        
        // Header is in row 1 (index 1)
        if (!isset($csv[1])) return $kelasColumns;
        
        $headerRow = $csv[1];
        
        foreach ($headerRow as $index => $cell) {
            $cell = trim($cell);
            
            // Match pattern like "X TKJ 1", "XI TKR 2", etc
            if (preg_match('/^(X{1,3}I*)\s+(TK[JR]|TKRO|TJKT)\s+(\d+)$/', $cell, $matches)) {
                $kelasColumns[$index] = $cell;
            }
        }
        
        return $kelasColumns;
    }

    /**
     * Parse jam from waktu string
     */
    private function parseJam($waktuStr)
    {
        $waktuStr = trim($waktuStr);
        
        // Format: "07.15 - 07.55" or "07.15-07.55"
        if (preg_match('/(\d{2})\.(\d{2})\s*-\s*(\d{2})\.(\d{2})/', $waktuStr, $matches)) {
            return [
                'mulai' => $matches[1] . ':' . $matches[2] . ':00',
                'selesai' => $matches[3] . ':' . $matches[4] . ':00',
            ];
        }
        
        return null;
    }
}
