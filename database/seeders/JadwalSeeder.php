<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Guru;
use App\Models\Kelas;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil guru dan kelas yang ada
        $guru = Guru::first();
        $kelas = Kelas::all();

        if (!$guru || $kelas->isEmpty()) {
            $this->command->warn('Tidak ada data guru atau kelas. Jalankan seeder guru dan kelas terlebih dahulu.');
            return;
        }

        $jadwalData = [];

        // Buat jadwal untuk setiap kelas
        foreach ($kelas as $k) {
            $jadwalData[] = [
                'id_kelas' => $k->id_kelas,
                'id_guru' => $guru->id_guru,
                'mata_pelajaran' => 'Matematika',
                'hari' => 'Senin',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:30:00',
                'created_at' => now(),
            ];

            $jadwalData[] = [
                'id_kelas' => $k->id_kelas,
                'id_guru' => $guru->id_guru,
                'mata_pelajaran' => 'Bahasa Indonesia',
                'hari' => 'Selasa',
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '10:00:00',
                'created_at' => now(),
            ];

            $jadwalData[] = [
                'id_kelas' => $k->id_kelas,
                'id_guru' => $guru->id_guru,
                'mata_pelajaran' => 'Bahasa Inggris',
                'hari' => 'Rabu',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:30:00',
                'created_at' => now(),
            ];
        }

        Jadwal::insert($jadwalData);

        $this->command->info('Jadwal seeder berhasil dijalankan!');
    }
}
