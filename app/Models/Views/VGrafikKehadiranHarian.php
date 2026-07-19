<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VGrafikKehadiranHarian extends Model
{
    protected $table = 'v_grafik_kehadiran_harian';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'tanggal' => 'date',
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_sakit' => 'integer',
        'total_alpha' => 'integer',
        'total_presensi' => 'integer',
    ];
}
