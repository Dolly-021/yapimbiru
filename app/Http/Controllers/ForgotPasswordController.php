<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use App\Services\FontteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    protected $fontte;

    public function __construct(FontteService $fontte)
    {
        $this->fontte = $fontte;
    }

    /**
     * Tampilkan form forgot password
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password-custom');
    }

    /**
     * Proses permintaan reset password
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ], [
            'identifier.required' => 'NIS/NIP/Email harus diisi',
        ]);

        try {
            // Cari user berdasarkan NIS/NIP/Email
            $user = User::where('email', $request->identifier)
                ->orWhereHas('siswa', function ($query) use ($request) {
                    $query->where('nis', $request->identifier);
                })
                ->orWhereHas('guru', function ($query) use ($request) {
                    $query->where('nip', $request->identifier);
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan'
                ], 404);
            }

            // Cek apakah user punya nomor HP
            $phoneNumber = null;
            if ($user->role === 'siswa' && $user->siswa) {
                $phoneNumber = $user->siswa->no_hp ?? null;
            } elseif ($user->role === 'guru' && $user->guru) {
                $phoneNumber = $user->guru->no_hp ?? null;
            }

            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor HP tidak terdaftar di sistem'
                ], 400);
            }

            // Format nomor HP ke format internasional jika diperlukan
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Hapus request sebelumnya yang masih pending
            PasswordReset::where('identifier', $request->identifier)
                ->where('status', 'pending')
                ->delete();

            // Generate OTP dan token
            $otp = PasswordReset::generateOtp();
            $token = PasswordReset::generateToken();

            // Simpan ke database
            $passwordReset = PasswordReset::create([
                'identifier' => $request->identifier,
                'no_hp' => $phoneNumber,
                'otp' => $otp,
                'token' => $token,
                'status' => 'pending',
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Kirim OTP via WhatsApp
            $username = $user->nama_lengkap ?? $user->username ?? 'Pengguna';
            $result = $this->fontte->sendOtp($phoneNumber, $otp, $username);

            if (!$result['success']) {
                // Hapus record jika gagal kirim
                $passwordReset->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengirim OTP via WhatsApp'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP telah dikirim ke WhatsApp Anda',
                'token' => $token,
                'phone' => $this->maskPhoneNumber($phoneNumber)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in sendOtp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        try {
            $passwordReset = PasswordReset::where('token', $request->token)
                ->where('status', 'pending')
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah expired'
                ], 404);
            }

            // Cek apakah OTP sudah expired
            if (!$passwordReset->isOtpValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP sudah expired, silakan request OTP baru'
                ], 400);
            }

            // Cek attempt
            if ($passwordReset->attempts >= 5) {
                $passwordReset->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak attempt gagal, silakan request OTP baru'
                ], 400);
            }

            // Cek OTP
            if ($passwordReset->otp !== $request->otp) {
                $passwordReset->increment('attempts');
                $remaining = 5 - $passwordReset->attempts;
                
                return response()->json([
                    'success' => false,
                    'message' => "OTP salah. Sisa attempt: {$remaining}",
                    'remaining' => $remaining
                ], 400);
            }

            // OTP benar, update status
            $passwordReset->update([
                'status' => 'verified',
                'verified_at' => Carbon::now(),
                'attempts' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP berhasil diverifikasi',
                'token' => $request->token
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in verifyOtp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Password tidak cocok',
        ]);

        try {
            $passwordReset = PasswordReset::where('token', $request->token)
                ->where('status', 'verified')
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 404);
            }

            // Cari user
            $user = User::where('email', $passwordReset->identifier)
                ->orWhereHas('siswa', function ($query) use ($passwordReset) {
                    $query->where('nis', $passwordReset->identifier);
                })
                ->orWhereHas('guru', function ($query) use ($passwordReset) {
                    $query->where('nip', $passwordReset->identifier);
                })
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => bcrypt($request->password)
            ]);

            // Update status
            $passwordReset->update([
                'status' => 'completed'
            ]);

            // Kirim notifikasi
            $username = $user->nama_lengkap ?? $user->username ?? 'Pengguna';
            $this->fontte->sendPasswordResetNotification($passwordReset->no_hp, $username);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset. Silakan login dengan password baru Anda.',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in resetPassword: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format nomor HP ke format internasional
     */
    private function formatPhoneNumber($phone): string
    {
        // Hapus spasi dan karakter khusus
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum dimulai dengan 62, tambahkan
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Mask nomor HP untuk display
     */
    private function maskPhoneNumber($phone): string
    {
        return substr($phone, 0, -4) . '****';
    }

    /**
     * Tampilkan form OTP verification
     */
    public function showOtpForm($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (!$passwordReset || !$passwordReset->isOtpValid()) {
            return redirect()->route('forgot-password')
                ->with('error', 'Token tidak valid atau sudah expired');
        }

        return view('auth.verify-otp', [
            'token' => $token,
            'phone' => $this->maskPhoneNumber($passwordReset->no_hp)
        ]);
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->where('status', 'verified')
            ->first();

        if (!$passwordReset) {
            return redirect()->route('forgot-password')
                ->with('error', 'Token tidak valid');
        }

        return view('auth.reset-password-custom', [
            'token' => $token
        ]);
    }
}
