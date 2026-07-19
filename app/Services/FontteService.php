<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class FontteService
{
    private $apiKey;
    private $apiUrl = 'https://api.fonnte.com';
    private $sendingKey = 'n'; // Device key dari Fonnte (default 'n')

    public function __construct()
    {
        // Pastikan key ini ada di file .env kamu: FONNTE_API_KEY=xxxx
        $this->apiKey = config('services.fonnte.api_key');
        $this->sendingKey = config('services.fonnte.sending_key', 'n');
    }

    /**
     * Kirim OTP via WhatsApp
     * * @param string $phoneNumber Nomor HP (format: 62xxxxxxxxxx)
     * @param string $otp Kode OTP
     * @param string $username Nama user
     * @return array
     */
    public function sendOtp(string $phoneNumber, string $otp, string $username): array
    {
        try {
            $message = "Halo $username,\n\n";
            $message .= "Kode OTP Anda untuk reset password MSBD:\n";
            $message .= "🔐 *$otp*\n\n";
            $message .= "Kode ini berlaku selama 10 menit.\n";
            $message .= "Jangan bagikan kode ini ke siapa pun.\n\n";
            $message .= "Jika Anda tidak melakukan permintaan ini, abaikan pesan ini.";

            return $this->sendMessage($phoneNumber, $message);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim notifikasi password berhasil direset
     */
    public function sendPasswordResetNotification(string $phoneNumber, string $username): array
    {
        try {
            $message = "Halo $username,\n\n";
            $message .= "✅ Password Anda telah berhasil direset.\n";
            $message .= "Silakan login kembali dengan password baru Anda.\n\n";
            $message .= "Jika ada pertanyaan, hubungi admin.";

            return $this->sendMessage($phoneNumber, $message);
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Kirim pesan generic (CORE FUNCTION - SUDAH DIPERBAIKI)
     */
    private function sendMessage(string $phoneNumber, string $message): array
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'Fonnte API key tidak dikonfigurasi',
            ];
        }

        try {
            // PERBAIKAN: Menggunakan withoutVerifying() dan withHeaders()
            $response = Http::withoutVerifying() // Bypass SSL untuk Localhost
                ->withHeaders([
                    'Authorization' => $this->apiKey // Token Authorization yang benar
                ])
                ->timeout(30)
                ->post("{$this->apiUrl}/send", [
                    'target' => $phoneNumber,
                    'message' => $message,
                    'countryCode' => '62', // Indonesia
                    'device' => $this->sendingKey, // Device key
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'data' => $response->json()
                ];
            } else {
                // Menangkap detail error dari Fonnte (misal: disconnect, invalid number)
                return [
                    'success' => false,
                    'message' => 'Gagal mengirim pesan. Status: ' . $response->status(),
                    'error' => $response->json() 
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test koneksi ke Fonnte API (SUDAH DIPERBAIKI)
     */
    public function testConnection(): array
    {
        try {
            // PERBAIKAN: Menggunakan withoutVerifying() dan withHeaders()
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => $this->apiKey
                ])
                ->timeout(10)
                ->get("{$this->apiUrl}/info"); // Endpoint check info device

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Fonnte berhasil',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Koneksi gagal: ' . $response->status(),
                    'error' => $response->json()
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}