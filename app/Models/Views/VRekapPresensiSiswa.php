<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VRekapPresensiSiswa extends Model
{
    protected $table = 'v_rekap_presensi_siswa';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'total_pertemuan' => 'integer',
        'hadir' => 'integer',
        'izin' => 'integer',
        'sakit' => 'integer',
        'alpha' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
    ];
}
