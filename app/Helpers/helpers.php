<?php

if (!function_exists('db_procedure')) {
    /**
     * Get database procedure service instance
     */
    function db_procedure(): \App\Services\DatabaseProcedureService
    {
        return app(\App\Services\DatabaseProcedureService::class);
    }
}

if (!function_exists('db_function')) {
    /**
     * Get database function service instance
     */
    function db_function(): \App\Services\DatabaseFunctionService
    {
        return app(\App\Services\DatabaseFunctionService::class);
    }
}

if (!function_exists('log_activity')) {
    /**
     * Get log activity service instance
     */
    function log_activity(): \App\Services\LogActivityService
    {
        return app(\App\Services\LogActivityService::class);
    }
}

if (!function_exists('quick_log')) {
    /**
     * Quick log activity
     */
    function quick_log(string $tipe, string $deskripsi, ?int $userId = null)
    {
        $userId = $userId ?? auth()->user()?->id_user;
        return log_activity()->log($tipe, $userId, $deskripsi);
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size to human readable
     */
    function format_file_size($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('get_current_role')) {
    /**
     * Get current user role
     */
    function get_current_role(): ?string
    {
        return auth()->user()?->role;
    }
}

if (!function_exists('is_role')) {
    /**
     * Check if current user has specific role
     */
    function is_role(string $role): bool
    {
        return get_current_role() === $role;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return is_role('admin');
    }
}

if (!function_exists('is_kepala_sekolah')) {
    function is_kepala_sekolah(): bool
    {
        return is_role('kepala_sekolah');
    }
}

if (!function_exists('is_pembina')) {
    function is_pembina(): bool
    {
        return is_role('pembina');
    }
}

if (!function_exists('is_guru')) {
    function is_guru(): bool
    {
        return is_role('guru');
    }
}

if (!function_exists('is_siswa')) {
    function is_siswa(): bool
    {
        return is_role('siswa');
    }
}

if (!function_exists('get_first_name')) {
    /**
     * Get first name from full name
     */
    function get_first_name(?string $fullName = null): string
    {
        if (!$fullName && auth()->check()) {
            $fullName = auth()->user()->nama_lengkap;
        }
        
        if (!$fullName) {
            return 'User';
        }
        
        // Ambil kata pertama dari nama lengkap
        $words = explode(' ', trim($fullName));
        return $words[0];
    }
}
