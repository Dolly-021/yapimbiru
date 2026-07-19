<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalStatus extends Model
{
    protected $table = 'jadwal_status';
    protected $primaryKey = 'id_jadwal_status';
    
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    protected $fillable = [
        'id_jadwal',
        'id_guru',
        'tanggal',
        'status_pertemuan',
        'keterangan',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_user');
    }
}
