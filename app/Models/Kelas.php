<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'tahun_ajaran'
    ];

    protected $casts = [
        'tingkat' => 'integer',
    ];

    // Relationships
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(Jadwal::class, 'id_kelas', 'id_kelas');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_kelas', 'id_kelas');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'id_kelas', 'id_kelas');
    }
}
