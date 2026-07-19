<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $table = 'backup_log';
    protected $primaryKey = 'id_backup';
    public $timestamps = true;

    protected $fillable = [
        'nama_file',
        'ukuran_file',
        'lokasi_file',
        'dibuat_oleh',
        'keterangan',
    ];

    protected $casts = [
        'ukuran_file' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }
}
