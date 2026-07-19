<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Absen extends Model
{
    protected $table = 'absens';
    protected $fillable = [
        'guru_id',
        'kelas_id',
        'guru_kelas_mapel_id',
        'jam_buka',
        'jam_tutup',
        'status',
        'keterangan'
    ];
    protected $casts = [
        'jam_buka' => 'datetime',
        'jam_tutup' => 'datetime'
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id_guru');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id', 'id_kelas');
    }

    public function guruKelasMapel(): BelongsTo
    {
        return $this->belongsTo(GuruKelasMapel::class, 'guru_kelas_mapel_id', 'id_guru_kelas_mapel');
    }

    public function absenSiswas(): HasMany
    {
        return $this->hasMany(AbsenSiswa::class, 'absen_id');
    }
}
