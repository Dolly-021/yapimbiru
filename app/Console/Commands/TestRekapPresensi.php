<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RekapPresensiService;
use Illuminate\Support\Facades\DB;

class TestRekapPresensi extends Command
{
    protected $signature = 'rekap:test {bulan=12} {tahun=2025}';

    protected $description = 'Test rekap presensi generation';

    protected $rekapService;

    public function __construct(RekapPresensiService $rekapService)
    {
        parent::__construct();
        $this->rekapService = $rekapService;
    }

    public function handle()
    {
        $bulan = $this->argument('bulan');
        $tahun = $this->argument('tahun');

        $this->info("Testing Rekap Presensi - Bulan: {$bulan}, Tahun: {$tahun}");
        $this->line('');

        // Check users
        $totalGuru = DB::table('users')->where('role', 'guru')->count();
        $totalSiswa = DB::table('siswa')->count();
        
        $this->info("Total Guru: {$totalGuru}");
        $this->info("Total Siswa: {$totalSiswa}");
        $this->line('');

        // Check presensi data
        $presensiGuru = DB::table('presensi')
            ->whereRaw('MONTH(tanggal) = ? AND YEAR(tanggal) = ?', [$bulan, $tahun])
            ->count();
        
        $presensiSiswa = DB::table('presensi_siswa')
            ->whereRaw('MONTH(tanggal) = ? AND YEAR(tanggal) = ?', [$bulan, $tahun])
            ->count();

        $this->info("Presensi Guru ({$bulan}/{$tahun}): {$presensiGuru} records");
        $this->info("Presensi Siswa ({$bulan}/{$tahun}): {$presensiSiswa} records");
        $this->line('');

        // Generate rekap
        $this->info('Generating Rekap Guru...');
        $rekapGuru = $this->rekapService->generateRekapGuru($bulan, $tahun);
        $this->info("Result: " . count($rekapGuru) . " guru");
        
        if (count($rekapGuru) > 0) {
            foreach ($rekapGuru->take(3) as $item) {
                $this->line("  - {$item->nama}: hadir={$item->hadir}, izin={$item->izin}, sakit={$item->sakit}, alfa={$item->alfa}");
            }
        }
        $this->line('');

        $this->info('Generating Rekap Siswa...');
        $rekapSiswa = $this->rekapService->generateRekapSiswa($bulan, $tahun);
        $this->info("Result: " . count($rekapSiswa) . " siswa");
        
        if (count($rekapSiswa) > 0) {
            foreach ($rekapSiswa->take(3) as $item) {
                $this->line("  - {$item->nama} ({$item->nama_kelas}): hadir={$item->hadir}, izin={$item->izin}, sakit={$item->sakit}, alfa={$item->alfa}");
            }
        }

        $this->info('Test completed successfully!');
    }
}
