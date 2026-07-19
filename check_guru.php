<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$guru = \App\Models\Guru::where('nama_lengkap', 'Johanes Hasudungan Simatupang, S.Pd.')->first();
if ($guru) {
    echo "Guru ditemukan: " . $guru->nama_lengkap . " (ID: " . $guru->id_guru . ")\n\n";
    
    $mapels = \App\Models\GuruKelasMapel::where('id_guru', $guru->id_guru)->with(['kelas', 'tahunAjaran'])->get();
    echo "Total GuruKelasMapel: " . $mapels->count() . "\n";
    foreach ($mapels as $m) {
        echo "- " . $m->mata_pelajaran . " di " . $m->kelas->nama_kelas . "\n";
    }
    
    $jadwals = \App\Models\Jadwal::where('id_guru', $guru->id_guru)->get();
    echo "\nTotal Jadwal: " . $jadwals->count() . "\n";
    foreach ($jadwals as $j) {
        echo "- " . $j->mata_pelajaran . " di Kelas (Hari: " . $j->hari . ")\n";
    }
} else {
    echo "Guru tidak ditemukan\n";
}
