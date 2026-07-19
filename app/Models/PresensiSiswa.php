<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresensiSiswa extends Model
{
    protected $table = 'presensi_siswa';

    protected $primaryKey = 'id_presensi_siswa';

    protected $fillable = [
        'id_siswa',
        'id_jadwal',
        'tanggal',
        'status',
        'keterangan',
        'status_verifikasi',
        'di_verifikasi_oleh',
        'alasan_reject',
        'diverifikasi_at',
        'diinput_oleh_tipe',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'diverifikasi_at' => 'datetime',
    ];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Relasi ke Jadwal
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    // Relasi ke User yang verifikasi
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'di_verifikasi_oleh', 'id_user');
    }
}
