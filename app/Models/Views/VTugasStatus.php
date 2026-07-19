<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VTugasStatus extends Model
{
    protected $table = 'v_tugas_status';
    public $timestamps = false;
    public $incrementing = false;

    protected $casts = [
        'deadline' => 'datetime',
        'total_siswa' => 'integer',
        'sudah_mengumpulkan' => 'integer',
        'belum_mengumpulkan' => 'integer',
    ];
}
