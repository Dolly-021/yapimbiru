<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VGrafikKehadiranSiswaHarian extends Model
{
    protected $table = 'v_grafik_kehadiran_siswa_harian';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'tanggal' => 'date',
        'tingkat' => 'integer',
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_sakit' => 'integer',
        'total_alpha' => 'integer',
        'total_presensi' => 'integer',
    ];
}
