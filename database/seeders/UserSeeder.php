<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'nama_lengkap' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => 'admin123',
                'role' => 'admin'
            ],
            [
                'username' => 'guru',
                'nama_lengkap' => 'Guru Demo',
                'email' => 'guru@example.com',
                'password' => 'password',
                'role' => 'guru'
            ],
            [
                'username' => 'siswa',
                'nama_lengkap' => 'Siswa Demo',
                'email' => 'siswa@example.com',
                'password' => 'password',
                'role' => 'siswa'
            ],
            [
                'username' => 'kepala_sekolah',
                'nama_lengkap' => 'Kepala Sekolah Demo',
                'email' => 'kepala_sekolah@example.com',
                'password' => 'password',
                'role' => 'kepala_sekolah'
            ],
            [
                'username' => 'pembina',
                'nama_lengkap' => 'Pembina Demo',
                'email' => 'pembina@example.com',
                'password' => 'password',
                'role' => 'pembina'
            ],
        ];

        foreach ($users as $u) {
            $password = $u['password'];
            unset($u['password']);
            
            User::updateOrCreate(
                ['email' => $u['email']],
                array_merge($u, [
                    'password' => Hash::make($password),
                    'status_aktif' => true
                ])
            );
        }
    }
}
