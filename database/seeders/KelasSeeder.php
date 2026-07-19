<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            // Kelas 10 - TO (Teknik Otomotif)
            ['nama_kelas' => 'X-TO-1', 'tingkat' => 10, 'jurusan' => 'Teknik Otomotif', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TO-2', 'tingkat' => 10, 'jurusan' => 'Teknik Otomotif', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TO-3', 'tingkat' => 10, 'jurusan' => 'Teknik Otomotif', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TO-4', 'tingkat' => 10, 'jurusan' => 'Teknik Otomotif', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TO-5', 'tingkat' => 10, 'jurusan' => 'Teknik Otomotif', 'tahun_ajaran' => '2025/2026'],
            
            // Kelas 10 - TJKT (Teknik Jaringan Komputer dan Telekomunikasi)
            ['nama_kelas' => 'X-TJKT-1', 'tingkat' => 10, 'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TJKT-2', 'tingkat' => 10, 'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TJKT-3', 'tingkat' => 10, 'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'X-TJKT-4', 'tingkat' => 10, 'jurusan' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'tahun_ajaran' => '2025/2026'],
            
            // Kelas 11 - TKJ (Teknik Komputer dan Jaringan)
            ['nama_kelas' => 'XI-TKJ-1', 'tingkat' => 11, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKJ-2', 'tingkat' => 11, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKJ-3', 'tingkat' => 11, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKJ-4', 'tingkat' => 11, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKJ-5', 'tingkat' => 11, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            
            // Kelas 11 - TKR (Teknik Kendaraan Ringan)
            ['nama_kelas' => 'XI-TKR-1', 'tingkat' => 11, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKR-2', 'tingkat' => 11, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKR-3', 'tingkat' => 11, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKR-4', 'tingkat' => 11, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XI-TKR-5', 'tingkat' => 11, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            
            // Kelas 12 - TKR (Teknik Kendaraan Ringan)
            ['nama_kelas' => 'XII-TKR-1', 'tingkat' => 12, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKR-2', 'tingkat' => 12, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKR-3', 'tingkat' => 12, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKR-4', 'tingkat' => 12, 'jurusan' => 'Teknik Kendaraan Ringan', 'tahun_ajaran' => '2025/2026'],
            
            // Kelas 12 - TKJ (Teknik Komputer dan Jaringan)
            ['nama_kelas' => 'XII-TKJ-1', 'tingkat' => 12, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKJ-2', 'tingkat' => 12, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKJ-3', 'tingkat' => 12, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
            ['nama_kelas' => 'XII-TKJ-4', 'tingkat' => 12, 'jurusan' => 'Teknik Komputer dan Jaringan', 'tahun_ajaran' => '2025/2026'],
        ];

        foreach ($kelas as $item) {
            DB::table('kelas')->insert([
                'nama_kelas' => $item['nama_kelas'],
                'tingkat' => $item['tingkat'],
                'jurusan' => $item['jurusan'],
                'tahun_ajaran' => $item['tahun_ajaran'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
