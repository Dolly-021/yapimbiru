<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSiswaMaster extends Model
{
    use HasFactory;

    protected $table = 'data_siswa_master';
    protected $primaryKey = 'id_siswa_master';

    protected $fillable = [
        'nis',
        'nama_siswa',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'usia',
        'agama',
        'sekolah_asal',
        'alamat',
        'no_hp',
        'id_kelas',
        'nama_kelas',
        'is_registered',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_registered' => 'boolean',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
