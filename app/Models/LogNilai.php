<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogNilai extends Model
{
    protected $table = 'log_nilai';
    protected $primaryKey = 'id_log';
    public $timestamps = false;

    protected $fillable = [
        'id_pengumpulan',
        'id_siswa',
        'nilai_lama',
        'nilai_baru',
    ];

    protected $casts = [
        'nilai_lama' => 'decimal:2',
        'nilai_baru' => 'decimal:2',
        'diubah_pada' => 'datetime',
    ];

    // Relationships
    public function pengumpulan()
    {
        return $this->belongsTo(PengumpulanTugas::class, 'id_pengumpulan', 'id_pengumpulan');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }
}
