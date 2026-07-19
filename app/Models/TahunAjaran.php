<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';
    
    protected $fillable = [
        'tahun_mulai',
        'tahun_selesai',
        'semester',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'tahun_mulai' => 'integer',
        'tahun_selesai' => 'integer'
    ];
    
    // Relationship: tahun ajaran memiliki banyak kelas
    public function kelasAjaran()
    {
        return $this->hasMany(KelasAjaran::class, 'id_tahun_ajaran');
    }
    
    // Relationship: tahun ajaran memiliki banyak penugasan guru
    public function guruKelasMapel()
    {
        return $this->hasMany(GuruKelasMapel::class, 'id_tahun_ajaran');
    }
    
    // Scope untuk tahun ajaran aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    // Helper: format tahun ajaran
    public function getFormatAttribute()
    {
        return "{$this->tahun_mulai}/{$this->tahun_selesai} - " . ucfirst($this->semester);
    }
}
