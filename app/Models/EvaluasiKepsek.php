<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluasiKepsek extends Model
{
    protected $table = 'evaluasi_kepsek';
    protected $primaryKey = 'id_evaluasi';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'id_target_user',
        'tipe',
        'isi_evaluasi',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function laporan()
    {
        return $this->belongsTo(LaporanAktivitas::class, 'id_laporan', 'id_laporan');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'id_target_user', 'id_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
