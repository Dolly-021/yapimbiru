<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pembina;
use Illuminate\Support\Facades\Hash;

class PembinaSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * 
     * Password default: password123
     * Untuk production, segera ganti password setelah login pertama kali
     */
    public function run(): void
    {
        // Cek apakah user pembina sudah ada
        $existingUser = User::where('username', 'pembina1')->first();
        
        if ($existingUser) {
            $this->command->info('User pembina1 sudah ada, skip seeding.');
            return;
        }

        // Create User
        $user = User::create([
            'username' => 'pembina1',
            'nama_lengkap' => 'Pembina Utama',
            'email' => 'pembina@smk.sch.id',
            'password' => Hash::make('password123'), // Default password
            'role' => 'pembina',
            'no_hp' => '081234567890',
            'status_aktif' => 1
        ]);

        // Create Pembina
        Pembina::create([
            'id_user' => $user->id_user,
            'nip' => '198501012010011001',
            'alamat' => 'Jalan Pembina No. 1',
            'tanggal_lahir' => '1985-01-01'
        ]);

        $this->command->info('✅ Pembina berhasil dibuat!');
        $this->command->warn('⚠️  Username: pembina1');
        $this->command->warn('⚠️  Password: password123');
        $this->command->warn('⚠️  SEGERA GANTI PASSWORD setelah login pertama kali!');
    }
}
