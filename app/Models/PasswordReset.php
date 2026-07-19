<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    protected $table = 'password_resets';
    
    protected $fillable = [
        'identifier',
        'no_hp',
        'otp',
        'token',
        'status',
        'attempts',
        'otp_expires_at',
        'verified_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if OTP masih valid (belum expired)
     */
    public function isOtpValid(): bool
    {
        return $this->status === 'pending' && 
               $this->otp_expires_at && 
               Carbon::now()->lessThan($this->otp_expires_at);
    }

    /**
     * Check if sudah bisa retry (setelah 1 menit dari attempt terakhir)
     */
    public function canRetry(): bool
    {
        return $this->updated_at->addMinute()->lessThanOrEqualTo(Carbon::now());
    }

    /**
     * Generate OTP 6 digit
     */
    public static function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate secure token
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
