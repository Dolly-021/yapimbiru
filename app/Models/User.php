<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id_user
 * @property string $username
 * @property string $nama_lengkap
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $no_hp
 * @property bool $status_aktif
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama tabel dan primary key
    protected $table = 'users';
    protected $primaryKey = 'id_user';

    // Kalau primary key bukan auto-increment integer, tambahkan ini:
    // public $incrementing = true;
    // protected $keyType = 'int';

    /**
     * Kolom yang boleh diisi mass-assignment
     */
    protected $fillable = [
        'username',
        'nama_lengkap',
        'email',
        'password',
        'role',
        'no_hp',
        'status_aktif',
        'status_approval',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Accessor untuk kompatibilitas dengan $user->name
     */
    public function getNameAttribute()
    {
        return $this->nama_lengkap;
    }

    /**
     * RELASI
     */
    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_user', 'id_user');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id_user', 'id_user');
    }

    public function kepalaSekolah()
    {
        return $this->hasOne(KepalaSekolah::class, 'id_user', 'id_user');
    }

    public function pembina()
    {
        return $this->hasOne(Pembina::class, 'id_user', 'id_user');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_user', 'id_user');
    }

    public function izin()
    {
        return $this->hasMany(Izin::class, 'id_user', 'id_user');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'id_user', 'id_user');
    }

    public function backupLog()
    {
        return $this->hasMany(BackupLog::class, 'dibuat_oleh', 'id_user');
    }

    /**
     * SCOPES
     */
    public function scopeActive($query)
    {
        return $query->where('status_aktif', 1);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeGuru($query)
    {
        return $query->where('role', 'guru');
    }

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function scopeKepalaSekolah($query)
    {
        return $query->where('role', 'kepala_sekolah');
    }

    public function scopePembina($query)
    {
        return $query->where('role', 'pembina');
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }
}
