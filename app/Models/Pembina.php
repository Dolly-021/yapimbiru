<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembina extends Model
{
    protected $table = 'pembina';
    protected $primaryKey = 'id_pembina';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nip',
        'alamat',
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

    public function laporanAktivitas()
    {
        return $this->hasMany(LaporanAktivitas::class, 'id_pembina', 'id_pembina');
    }
}

