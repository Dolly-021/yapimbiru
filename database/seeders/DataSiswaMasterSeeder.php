<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataSiswaMaster;
use Carbon\Carbon;

class DataSiswaMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping nama file CSV ke id_kelas
        $kelasMapping = [
            // Kelas X-TO (Teknik Otomotif)
            'x-to-1.csv' => 1,
            'x-to-2.csv' => 2,
            'x-to-3.csv' => 3,
            'x-to-4.csv' => 4,
            'x-to-5.csv' => 5,
            // Kelas X-TJKT (Teknik Jaringan Komputer dan Telekomunikasi)
            'x-tjkt-1.csv' => 6,
            'x-tjkt-2.csv' => 7,
            'x-tjkt-3.csv' => 8,
            'x-tjkt-4.csv' => 9,
            // Kelas XI-TKJ (Teknik Komputer dan Jaringan)
            'xi-tkj-1.csv' => 10,
            'xi-tkj-2.csv' => 11,
            'xi-tkj-3.csv' => 12,
            'xi-tkj-4.csv' => 13,
            'xi-tkj-5.csv' => 14,
            // Kelas XI-TKR (Teknik Kendaraan Ringan)
            'xi-tkr-1.csv' => 15,
            'xi-tkr-2.csv' => 16,
            'xi-tkr-3.csv' => 17,
            'xi-tkr-4.csv' => 18,
            'xi-tkr-5.csv' => 19,
            // Kelas XII-TKR (Teknik Kendaraan Ringan)
            'xii-tkr-1.csv' => 20,
            'xii-tkr-2.csv' => 21,
            'xii-tkr-3.csv' => 22,
            'xii-tkr-4.csv' => 23,
            // Kelas XII-TKJ (Teknik Komputer dan Jaringan)
            'xii-tkj-1.csv' => 24,
            'xii-tkj-2.csv' => 25,
            'xii-tkj-3.csv' => 26,
            'xii-tkj-4.csv' => 27,
        ];

        $csvPath = base_path('excel');

        foreach ($kelasMapping as $filename => $idKelas) {
            $filePath = $csvPath . '/' . $filename;
            
            if (!file_exists($filePath)) {
                $this->command->warn("File tidak ditemukan: {$filename}");
                continue;
            }

            $this->command->info("Processing: {$filename}");
            $this->importCsv($filePath, $idKelas);
        }

        $this->command->info('Data siswa master berhasil diimport!');
    }

    private function importCsv($filePath, $idKelas)
    {
        $file = fopen($filePath, 'r');
        $headers = null;
        $rowNumber = 0;
        $namaKelas = $this->getNamaKelas($idKelas);

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $rowNumber++;

            // Skip 3 baris pertama (header excel)
            if ($rowNumber <= 3) {
                continue;
            }

            // Baris ke-4 adalah header kolom
            if ($rowNumber == 4) {
                $headers = $row;
                continue;
            }

            // Skip baris kosong
            if (empty($row[0]) || trim($row[0]) === '') {
                continue;
            }

            try {
                // Parse data
                $nis = trim($row[8] ?? ''); // Kolom NIS
                if (empty($nis)) continue;

                $namaSiswa = trim($row[1] ?? '');
                $jenisKelamin = strtoupper(trim($row[2] ?? ''));
                $tempatTglLahir = trim($row[3] ?? '');
                $usia = (int) trim($row[4] ?? 0);
                $sekolahAsal = trim($row[7] ?? '');
                $agama = trim($row[9] ?? '');
                $alamat = trim($row[10] ?? '');
                $noHp = trim($row[11] ?? '');

                // Parse tempat dan tanggal lahir
                [$tempatLahir, $tanggalLahir] = $this->parseTempatTanggalLahir($tempatTglLahir);

                DataSiswaMaster::updateOrCreate(
                    ['nis' => $nis],
                    [
                        'nama_siswa' => $namaSiswa,
                        'jenis_kelamin' => $jenisKelamin === 'P' ? 'P' : 'L',
                        'tempat_lahir' => $tempatLahir,
                        'tanggal_lahir' => $tanggalLahir,
                        'usia' => $usia,
                        'agama' => $agama,
                        'sekolah_asal' => $sekolahAsal,
                        'alamat' => $alamat,
                        'no_hp' => $noHp ?: null,
                        'id_kelas' => $idKelas,
                        'nama_kelas' => $namaKelas,
                        'is_registered' => false,
                    ]
                );

            } catch (\Exception $e) {
                $this->command->error("Error di baris {$rowNumber}: " . $e->getMessage());
            }
        }

        fclose($file);
    }

    private function parseTempatTanggalLahir($tempatTglLahir)
    {
        if (empty($tempatTglLahir)) {
            return [null, null];
        }

        // Format: "Lima Puluh Kota, 10 Maret 2010"
        $parts = explode(',', $tempatTglLahir, 2);
        $tempatLahir = trim($parts[0] ?? '');
        $tanggalStr = trim($parts[1] ?? '');

        if (empty($tanggalStr)) {
            return [$tempatLahir, null];
        }

        try {
            // Array bulan Indonesia
            $bulanIndo = [
                'Januari' => '01', 'Februari' => '02', 'Maret' => '03',
                'April' => '04', 'Mei' => '05', 'Juni' => '06',
                'Juli' => '07', 'Agustus' => '08', 'September' => '09',
                'Oktober' => '10', 'Nopember' => '11', 'November' => '11', 'Desember' => '12'
            ];

            // Parse "10 Maret 2010"
            foreach ($bulanIndo as $indo => $numeric) {
                if (stripos($tanggalStr, $indo) !== false) {
                    $tanggalStr = str_ireplace($indo, $numeric, $tanggalStr);
                    break;
                }
            }

            // Remove extra spaces
            $tanggalStr = preg_replace('/\s+/', ' ', $tanggalStr);
            $parts = explode(' ', $tanggalStr);

            if (count($parts) >= 3) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                $year = $parts[2];
                $tanggalLahir = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                return [$tempatLahir, $tanggalLahir];
            }
        } catch (\Exception $e) {
            // Jika parsing gagal, return null untuk tanggal
            return [$tempatLahir, null];
        }

        return [$tempatLahir, null];
    }

    private function getNamaKelas($idKelas)
    {
        $kelasNames = [
            1 => 'X-TO-1', 2 => 'X-TO-2', 3 => 'X-TO-3', 4 => 'X-TO-4', 5 => 'X-TO-5',
            6 => 'X-TJKT-1', 7 => 'X-TJKT-2', 8 => 'X-TJKT-3', 9 => 'X-TJKT-4',
            10 => 'XI-TKJ-1', 11 => 'XI-TKJ-2', 12 => 'XI-TKJ-3', 13 => 'XI-TKJ-4', 14 => 'XI-TKJ-5',
            15 => 'XI-TKR-1', 16 => 'XI-TKR-2', 17 => 'XI-TKR-3', 18 => 'XI-TKR-4', 19 => 'XI-TKR-5',
            20 => 'XII-TKR-1', 21 => 'XII-TKR-2', 22 => 'XII-TKR-3', 23 => 'XII-TKR-4',
            24 => 'XII-TKJ-1', 25 => 'XII-TKJ-2', 26 => 'XII-TKJ-3', 27 => 'XII-TKJ-4',
        ];

        return $kelasNames[$idKelas] ?? '';
    }
}
