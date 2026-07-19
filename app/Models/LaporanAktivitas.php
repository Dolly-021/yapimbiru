<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAktivitas extends Model
{
    protected $table = 'laporan_aktivitas';
    protected $primaryKey = 'id_laporan';
    
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'id_pembina',
        'id_guru',
        'periode_bulan',
        'periode_tahun',
        'judul_laporan',
        'isi_laporan',
        'file_pdf',
        'status',
        'catatan_kepsek',
        'reviewed_at',
    ];

    protected $casts = [
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function pembina()
    {
        return $this->belongsTo(Pembina::class, 'id_pembina', 'id_pembina');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function evaluasi()
    {
        return $this->hasMany(EvaluasiKepsek::class, 'id_laporan', 'id_laporan');
    }
}
