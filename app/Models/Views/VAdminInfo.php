<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VAdminInfo extends Model
{
    protected $table = 'v_admin_info';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'id_admin';

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
    ];
}
