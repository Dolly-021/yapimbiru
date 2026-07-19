<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\LogAktivitas;

class LogActivityService
{
    /**
     * Log aktivitas pengguna
     */
    public function log(string $tipeAktivitas, ?int $idUser, string $deskripsi, ?string $ipAddress = null, ?string $userAgent = null)
    {
        try {
            return LogAktivitas::create([
                'tipe_aktivitas' => $tipeAktivitas,
                'id_user' => $idUser,
                'deskripsi' => $deskripsi,
                'ip_address' => $ipAddress ?? request()->ip() ?? '127.0.0.1',
                'user_agent' => $userAgent ?? request()->userAgent() ?? 'Unknown',
            ]);
        } catch (\Exception $e) {
            // Fallback jika gagal insert log
            \Log::error('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log login
     */
    public function logLogin(int $idUser)
    {
        return $this->log('login', $idUser, 'User login ke sistem');
    }

    /**
     * Log logout
     */
    public function logLogout(int $idUser)
    {
        return $this->log('logout', $idUser, 'User logout dari sistem');
    }

    /**
     * Log approval izin
     */
    public function logApprovalIzin(int $idUser, int $idIzin, string $status)
    {
        return $this->log(
            'approval_izin',
            $idUser,
            "Mengubah status izin ID {$idIzin} menjadi {$status}"
        );
    }

    /**
     * Log create/update/delete
     */
    public function logCrud(string $action, int $idUser, string $tableName, $recordId)
    {
        $actions = [
            'create' => 'Menambah',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
        ];

        $actionText = $actions[$action] ?? $action;

        return $this->log(
            $action,
            $idUser,
            "{$actionText} data {$tableName} dengan ID {$recordId}"
        );
    }
}
