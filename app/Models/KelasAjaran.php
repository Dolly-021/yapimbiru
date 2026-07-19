<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasAjaran extends Model
{
    protected $table = 'kelas_tahun_ajaran';
    protected $primaryKey = 'id_kelas_tahun_ajaran';
    
    protected $fillable = [
        'id_kelas',
        'id_tahun_ajaran',
        'id_guru_wali'
    ];
    
    // Relationship: belongs to kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
    
    // Relationship: belongs to tahun ajaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }
    
    // Relationship: belongs to guru (wali kelas)
    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'id_guru_wali');
    }
}
