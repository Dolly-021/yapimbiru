<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VStatusIzinSiswa extends Model
{
    protected $table = 'v_status_izin_siswa';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'tanggal' => 'date',
        'approved_at' => 'datetime',
        'tanggal_pengajuan' => 'datetime',
    ];
}
