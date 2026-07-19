<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Scopes
    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeIzin($query)
    {
        return $query->where('status', 'izin');
    }

    public function scopeSakit($query)
    {
        return $query->where('status', 'sakit');
    }

    public function scopeAlpha($query)
    {
        return $query->where('status', 'alpha');
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)
                     ->whereYear('tanggal', $year);
    }
}
