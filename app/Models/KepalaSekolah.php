<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KepalaSekolah extends Model
{
    protected $table = 'kepala_sekolah';
    protected $primaryKey = 'id_kepsek';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nip',
        'periode_mulai',
        'alamat',
        'tanggal_lahir',
    ];

    protected $casts = [
        'periode_mulai' => 'date',
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}

