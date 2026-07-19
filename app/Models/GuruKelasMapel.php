<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruKelasMapel extends Model
{
    protected $table = 'guru_kelas_mapel';
    protected $primaryKey = 'id_guru_kelas_mapel';
    
    protected $fillable = [
        'id_guru',
        'id_kelas',
        'id_tahun_ajaran',
        'mata_pelajaran'
    ];
    
    // Relationship: belongs to guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
    
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
    
    // Scope: get mapel untuk guru tertentu di tahun ajaran aktif
    public function scopeForGuruActiveYear($query, $idGuru)
    {
        return $query->where('id_guru', $idGuru)
                    ->whereHas('tahunAjaran', function($q) {
                        $q->where('is_active', true);
                    });
    }
    
    // Scope: check if guru mengajar di kelas tertentu
    public function scopeCanTeachKelas($query, $idGuru, $idKelas, $idTahunAjaran = null)
    {
        $query = $query->where('id_guru', $idGuru)
                      ->where('id_kelas', $idKelas);
        
        if ($idTahunAjaran) {
            $query->where('id_tahun_ajaran', $idTahunAjaran);
        } else {
            $query->whereHas('tahunAjaran', function($q) {
                $q->where('is_active', true);
            });
        }
        
        return $query;
    }
}
