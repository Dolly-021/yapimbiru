<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal_pelajaran';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'id_guru',
        'mata_pelajaran',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function statusJadwal()
    {
        return $this->hasMany(JadwalStatus::class, 'id_jadwal', 'id_jadwal');
    }

    public function presensiSiswa()
    {
        return $this->hasMany(PresensiSiswa::class, 'id_jadwal', 'id_jadwal');
    }

    // Scopes
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }
}
