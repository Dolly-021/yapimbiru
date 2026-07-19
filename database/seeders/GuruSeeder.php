<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Guru;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data guru berdasarkan file CSV nama_guru.csv
        $dataGuru = [
            [
                'nama_lengkap' => 'Himru Lumban Batu, ST., Gr',
                'jabatan' => 'Ketua Konsentrasi Keahlian TKR',
                'mata_pelajaran' => 'PKKR',
            ],
            [
                'nama_lengkap' => 'Benny Pinondang Naibaho, S.Pd., Gr.',
                'jabatan' => 'HUMAS IDUKA',
                'mata_pelajaran' => 'PSTKR, PMKR, PDTO, TDO',
            ],
            [
                'nama_lengkap' => 'Drs. Togar Sihotang',
                'jabatan' => null,
                'mata_pelajaran' => 'GTO, PDTO, PMKR, PKKR',
            ],
            [
                'nama_lengkap' => 'Santun Manurung, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'PMKR',
            ],
            [
                'nama_lengkap' => 'Dodi Pratama Perangi-Angin, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'PSPTKR, PMKR, PDTO',
            ],
            [
                'nama_lengkap' => 'Johanes Hasudungan Simatupang, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'PDTO, PSPTKR',
            ],
            [
                'nama_lengkap' => 'Jhonson Costantin Gultom, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Konversi Energi, PKK',
            ],
            [
                'nama_lengkap' => 'Marisnauli Situmorang, S.Pd., Gr.',
                'jabatan' => null,
                'mata_pelajaran' => 'PKK',
            ],
            [
                'nama_lengkap' => 'Lotar Mateus Sinaga, M.Kom.',
                'jabatan' => null,
                'mata_pelajaran' => 'AIJ, MPJBL',
            ],
            [
                'nama_lengkap' => 'Addy Suranta Pinem, S.Kom.',
                'jabatan' => 'Ketua Bursa Kerja Khusus',
                'mata_pelajaran' => 'TLJ, SK',
            ],
            [
                'nama_lengkap' => 'Angga Agnesta, S.Kom., Gr.',
                'jabatan' => null,
                'mata_pelajaran' => 'MPJBL, PKK, TLJ',
            ],
            [
                'nama_lengkap' => 'Eka Elisata Barus, S.Kom., Gr.',
                'jabatan' => 'Ketua Konsentrasi Keahlian TKJ',
                'mata_pelajaran' => 'JARDAS, AIJ, KKPI',
            ],
            [
                'nama_lengkap' => 'Ika Kurniawan, S.Kom., Gr.',
                'jabatan' => null,
                'mata_pelajaran' => 'Informatika, PKK, KKPI',
            ],
            [
                'nama_lengkap' => 'Imelia Rosa Sinuhaji, S.Kom.',
                'jabatan' => null,
                'mata_pelajaran' => 'Informatika, PD',
            ],
            [
                'nama_lengkap' => 'Dewi Sutriani Sembiring, S.Kom.',
                'jabatan' => null,
                'mata_pelajaran' => 'AIJ, ASJ',
            ],
            [
                'nama_lengkap' => 'Meliyani Ajelina Barus, S.Kom.',
                'jabatan' => null,
                'mata_pelajaran' => 'ASJ, PKK, JARDAS',
            ],
            [
                'nama_lengkap' => 'Hafiz Al Irsyad, S.Kom.',
                'jabatan' => null,
                'mata_pelajaran' => 'SK, ASJ',
            ],
            [
                'nama_lengkap' => 'Junellia Tianta Barus, S.Kom.',
                'jabatan' => 'Marketing, Sekretaris BKK',
                'mata_pelajaran' => 'KKPI, DDG',
            ],
            [
                'nama_lengkap' => 'Sriwati Barus, S.Ag.',
                'jabatan' => null,
                'mata_pelajaran' => 'Agama Katolik',
            ],
            [
                'nama_lengkap' => 'Ramana Sembiring, S.PdK.',
                'jabatan' => null,
                'mata_pelajaran' => 'Agama Kristen',
            ],
            [
                'nama_lengkap' => 'Warnaita Barus, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Agama Kristen',
            ],
            [
                'nama_lengkap' => 'Siti Fatimah, M.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Agama Islam',
            ],
            [
                'nama_lengkap' => 'Agustinus Laia, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Pend. Pancasila',
            ],
            [
                'nama_lengkap' => 'Corsalina Simamora, S.Pd., Gr.',
                'jabatan' => 'Pembina OSIS',
                'mata_pelajaran' => 'B. Indonesia',
            ],
            [
                'nama_lengkap' => 'Novita Anggraini, M.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'B. Indonesia',
            ],
            [
                'nama_lengkap' => 'Chandra Nainggolan, S.Pd.',
                'jabatan' => 'Pembina OSIS',
                'mata_pelajaran' => 'PJOK',
            ],
            [
                'nama_lengkap' => 'David Saputra Woffelson Sagala, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'PKK, Sej. Indonesia',
            ],
            [
                'nama_lengkap' => 'Miranti Sembiring, S.Sn.',
                'jabatan' => null,
                'mata_pelajaran' => 'Seni Musik',
            ],
            [
                'nama_lengkap' => 'Rifka Rahayu Siregar, S.Pd., Gr.',
                'jabatan' => 'Pembina Kesiswaan',
                'mata_pelajaran' => 'Matematika',
            ],
            [
                'nama_lengkap' => 'Rehmalemna Sitepu, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Matematika',
            ],
            [
                'nama_lengkap' => 'Hotmaida Ginting, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'Matematika',
            ],
            [
                'nama_lengkap' => 'Milia Friska Rani Hutajulu, S.Pd., Gr.',
                'jabatan' => 'Pembina Kurikulum',
                'mata_pelajaran' => 'B. Inggris, Lab. Bahasa',
            ],
            [
                'nama_lengkap' => 'Khairul Bariyah, SS',
                'jabatan' => null,
                'mata_pelajaran' => 'B. Inggris, Lab. Bahasa',
            ],
            [
                'nama_lengkap' => 'Dwi Nopita Sari, S.Pd., Gr.',
                'jabatan' => null,
                'mata_pelajaran' => 'B. Inggris, Lab. Bahasa',
            ],
            [
                'nama_lengkap' => 'Edison J.P Tumanggor, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'IPAS',
            ],
            [
                'nama_lengkap' => 'Iin Purwanti, S.Pd., Gr.',
                'jabatan' => null,
                'mata_pelajaran' => 'IPAS, PKK',
            ],
            [
                'nama_lengkap' => 'Khairul Amru Hasibuan, S.Pd.',
                'jabatan' => null,
                'mata_pelajaran' => 'BK',
            ],
        ];

        echo "Creating guru accounts...\n";

        $usedUsernames = [];
        foreach ($dataGuru as $index => $guru) {
            // Generate username dari nama (ambil nama depan dan belakang)
            $username = $this->generateUsername($guru['nama_lengkap']);
            
            // Handle duplicate usernames
            $originalUsername = $username;
            $counter = 1;
            while (in_array($username, $usedUsernames)) {
                $username = $originalUsername . $counter;
                $counter++;
            }
            $usedUsernames[] = $username;
            
            // Generate email
            $email = strtolower(str_replace('.', '', $username)) . '@smkyapim.sch.id';

            // Create user account
            $user = User::create([
                'username' => $username,
                'nama_lengkap' => $guru['nama_lengkap'],
                'email' => $email,
                'password' => Hash::make('guru123'), // Password default: guru123
                'role' => 'guru',
                'status_aktif' => true,
                'status_approval' => 'approved',
            ]);

            // Create guru record
            Guru::create([
                'id_user' => $user->id_user,
                'jenis_kelamin' => $this->guessGender($guru['nama_lengkap']),
                'agama' => $this->guessReligion($guru['nama_lengkap'], $guru['mata_pelajaran']),
                'jabatan' => $guru['jabatan'],
                'alamat' => 'Biru-Biru, Kabupaten Deli Serdang',
                'no_hp' => '0812' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'tanggal_lahir' => date('Y-m-d', strtotime('-' . rand(30, 50) . ' years')),
            ]);

            echo "✓ Created: {$guru['nama_lengkap']} (username: {$username})\n";
        }

        echo "\n" . count($dataGuru) . " guru accounts created successfully!\n";
        echo "Default password for all guru: guru123\n";
    }

    /**
     * Generate username from full name
     */
    private function generateUsername($namaLengkap): string
    {
        // Remove titles and degrees
        $nama = preg_replace('/\b(ST|S\.Pd|S\.Kom|S\.Ag|S\.PdK|S\.Sn|M\.Pd|M\.Kom|Drs|Gr|SS)\.?\b/i', '', $namaLengkap);
        $nama = preg_replace('/,\s*/', ' ', $nama);
        
        // Remove dots and extra spaces
        $nama = str_replace('.', '', $nama);
        $nama = preg_replace('/\s+/', ' ', $nama);
        $nama = trim($nama);

        // Split into words and remove empty ones
        $words = array_filter(explode(' ', $nama), function($word) {
            return strlen($word) > 0;
        });
        $words = array_values($words); // Reindex array
        
        // Generate username based on number of words
        if (count($words) == 0) {
            return 'guru';
        } elseif (count($words) == 1) {
            $username = strtolower($words[0]);
        } elseif (count($words) == 2) {
            $username = strtolower($words[0] . '.' . $words[1]);
        } else {
            // Use first and last word
            $username = strtolower($words[0] . '.' . end($words));
        }

        // Remove special characters except dots
        $username = preg_replace('/[^a-z0-9.]/', '', $username);

        return $username;
    }

    /**
     * Guess gender from name
     */
    private function guessGender($namaLengkap): string
    {
        // Common female first names in Batak/Indonesian culture
        $femaleNames = ['sri', 'dewi', 'iin', 'novita', 'milia', 'khairul bariyah', 'dwi nopita', 
                        'meliyani', 'imelia', 'junellia', 'marisnauli', 'corsalina', 'miranti', 
                        'rifka', 'rehmalemna', 'hotmaida', 'warnaita'];
        
        $namaLower = strtolower($namaLengkap);
        
        foreach ($femaleNames as $name) {
            if (str_contains($namaLower, $name)) {
                return 'P';
            }
        }
        
        return 'L'; // Default to male
    }

    /**
     * Guess religion from name or subject taught
     */
    private function guessReligion($namaLengkap, $mataPelajaran): string
    {
        if (str_contains(strtolower($mataPelajaran), 'katolik')) {
            return 'Katolik';
        }
        if (str_contains(strtolower($mataPelajaran), 'kristen')) {
            return 'Kristen';
        }
        if (str_contains(strtolower($mataPelajaran), 'islam')) {
            return 'Islam';
        }
        
        // Guess from name patterns
        $namaLower = strtolower($namaLengkap);
        
        // Muslim names
        if (str_contains($namaLower, 'siti') || str_contains($namaLower, 'hafiz') || 
            str_contains($namaLower, 'khairul')) {
            return 'Islam';
        }
        
        // Christian/Catholic names
        if (str_contains($namaLower, 'agustinus') || str_contains($namaLower, 'jhonson') ||
            str_contains($namaLower, 'david') || str_contains($namaLower, 'edison')) {
            return 'Kristen';
        }
        
        // Default based on Batak culture (mostly Christian)
        return 'Kristen';
    }
}
