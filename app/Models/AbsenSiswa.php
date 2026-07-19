<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsenSiswa extends Model
{
    protected $table = 'absen_siswas';
    protected $fillable = [
        'absen_id',
        'id_siswa',
        'status',
        'waktu_absen',
        'keterangan'
    ];
    protected $casts = [
        'waktu_absen' => 'datetime'
    ];

    public function absen(): BelongsTo
    {
        return $this->belongsTo(Absen::class, 'absen_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
