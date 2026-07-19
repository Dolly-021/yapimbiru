<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis_kelamin',
        'agama',
        'jabatan',
        'alamat',
        'no_hp',
        'tanggal_lahir',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_guru', 'id_guru');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_guru', 'id_guru');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'id_guru', 'id_guru');
    }

    public function laporanAktivitas()
    {
        return $this->hasMany(LaporanAktivitas::class, 'id_guru', 'id_guru');
    }
}

