<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VRekapPresensiGuru extends Model
{
    protected $table = 'v_rekap_presensi_guru';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'total_hari' => 'integer',
        'hadir' => 'integer',
        'izin' => 'integer',
        'sakit' => 'integer',
        'alpha' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
    ];
}
