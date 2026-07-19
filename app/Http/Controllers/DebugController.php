<?php
namespace App\Http\Controllers;

use App\Models\Guru;

class DebugController extends Controller
{
    public function checkGuru()
    {
        $gurus = Guru::all();
        
        echo "<h2>Semua Guru:</h2>";
        foreach ($gurus as $g) {
            echo "<p>" . $g->id_guru . " - " . $g->nama_lengkap . "</p>";
        }
        
        echo "<h2>Cari 'Johanes' atau 'Jhonson':</h2>";
        $johanes = Guru::where('nama_lengkap', 'like', '%Johan%')->get();
        foreach ($johanes as $g) {
            echo "<p>" . $g->id_guru . " - " . $g->nama_lengkap . "</p>";
            
            $mapels = $g->guruKelasMapels()->get();
            echo "  GuruKelasMapel: " . $mapels->count() . "<br>";
            
            $jadwals = $g->jadwals()->get();
            echo "  Jadwal: " . $jadwals->count() . "<br>";
        }
    }
}
