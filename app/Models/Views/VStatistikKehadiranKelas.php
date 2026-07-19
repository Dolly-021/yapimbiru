<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VStatistikKehadiranKelas extends Model
{
    protected $table = 'v_statistik_kehadiran_kelas';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'total_siswa_presensi' => 'integer',
        'total_pertemuan' => 'integer',
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_sakit' => 'integer',
        'total_alpha' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
    ];
}
