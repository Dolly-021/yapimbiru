<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RekapPresensiService
{
    /**
     * Generate rekap presensi guru untuk bulan dan tahun tertentu
     */
    public function generateRekapGuru($bulan, $tahun, $guruId = null)
    {
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $query = DB::table('users')
            ->leftJoin('presensi', function($join) use ($bulan, $tahun) {
                $join->on('users.id_user', '=', 'presensi.id_user')
                    ->whereRaw('MONTH(presensi.tanggal) = ?', [$bulan])
                    ->whereRaw('YEAR(presensi.tanggal) = ?', [$tahun]);
            })
            ->select(
                'users.id_user',
                'users.username',
                'users.nama_lengkap as nama',
                DB::raw('COUNT(CASE WHEN presensi.status = "hadir" THEN 1 END) as hadir'),
                DB::raw('COUNT(CASE WHEN presensi.status = "izin" THEN 1 END) as izin'),
                DB::raw('COUNT(CASE WHEN presensi.status = "sakit" THEN 1 END) as sakit'),
                DB::raw('COUNT(CASE WHEN presensi.status = "alpha" THEN 1 END) as alfa'),
                DB::raw('COUNT(presensi.id_presensi) as total_hari'),
                DB::raw('ROUND(COUNT(CASE WHEN presensi.status = "hadir" THEN 1 END) / NULLIF(COUNT(presensi.id_presensi), 0) * 100, 2) as persentase')
            )
            ->where('users.role', 'guru');
        
        // Apply filter if specified
        if ($guruId) {
            $query->where('users.id_user', $guruId);
        }
        
        return $query->groupBy('users.id_user', 'users.username', 'users.nama_lengkap')
            ->orderBy('users.nama_lengkap')
            ->get();
    }

    /**
     * Generate rekap presensi siswa untuk bulan dan tahun tertentu
     */
    public function generateRekapSiswa($bulan, $tahun, $kelasId = null)
    {
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        $query = DB::table('siswa')
            ->leftJoin('users', 'siswa.id_user', '=', 'users.id_user')
            ->leftJoin('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->leftJoin('presensi_siswa', function($join) use ($bulan, $tahun) {
                $join->on('siswa.id_siswa', '=', 'presensi_siswa.id_siswa')
                    ->whereRaw('MONTH(presensi_siswa.tanggal) = ?', [$bulan])
                    ->whereRaw('YEAR(presensi_siswa.tanggal) = ?', [$tahun]);
            })
            ->select(
                'siswa.id_siswa',
                'users.nama_lengkap as nama',
                'kelas.nama_kelas',
                DB::raw('COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) as hadir'),
                DB::raw('COUNT(CASE WHEN presensi_siswa.status = "izin" THEN 1 END) as izin'),
                DB::raw('COUNT(CASE WHEN presensi_siswa.status = "sakit" THEN 1 END) as sakit'),
                DB::raw('COUNT(CASE WHEN presensi_siswa.status = "alpha" THEN 1 END) as alfa'),
                DB::raw('COUNT(presensi_siswa.id_presensi_siswa) as total_hari'),
                DB::raw('ROUND(COUNT(CASE WHEN presensi_siswa.status = "hadir" THEN 1 END) / NULLIF(COUNT(presensi_siswa.id_presensi_siswa), 0) * 100, 2) as persentase')
            );
        
        // Apply filter by kelas if specified
        if ($kelasId) {
            $query->where('siswa.id_kelas', $kelasId);
        }
        
        return $query->groupBy('siswa.id_siswa', 'users.nama_lengkap', 'kelas.nama_kelas')
            ->orderBy('users.nama_lengkap')
            ->get();
    }

    /**
     * Fill the v_rekap_presensi tables
     */
    public function fillRekapTables($bulan, $tahun)
    {
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        
        // Clear existing data for this month/year
        DB::table('v_rekap_presensi_guru')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete();
            
        DB::table('v_rekap_presensi_siswa')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->delete();
        
        // Fill guru data
        $guruData = $this->generateRekapGuru($bulan, $tahun);
        foreach ($guruData as $data) {
            DB::table('v_rekap_presensi_guru')->insert([
                'id_user' => $data->id_user,
                'username' => $data->username,
                'nama_lengkap' => $data->nama,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'total_hadir' => $data->hadir ?? 0,
                'total_izin' => $data->izin ?? 0,
                'total_sakit' => $data->sakit ?? 0,
                'total_alpha' => $data->alfa ?? 0,
                'total_hari' => $data->total_hari ?? 0,
            ]);
        }
        
        // Fill siswa data
        $siswaData = $this->generateRekapSiswa($bulan, $tahun);
        foreach ($siswaData as $data) {
            DB::table('v_rekap_presensi_siswa')->insert([
                'id_siswa' => $data->id_siswa,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'nama' => $data->nama,
                'nama_kelas' => $data->nama_kelas,
                'hadir' => $data->hadir ?? 0,
                'izin' => $data->izin ?? 0,
                'sakit' => $data->sakit ?? 0,
                'alfa' => $data->alfa ?? 0,
                'total_hari' => $data->total_hari ?? 0,
                'persentase' => $data->persentase ?? 0,
            ]);
        }
    }
}
