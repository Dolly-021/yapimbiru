@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-user-shield"></i> Selamat Datang, {{ get_first_name() }}!</h2>
    <p>Halo <strong>{{ auth()->user()->nama_lengkap }}</strong>, Anda memiliki akses penuh terhadap seluruh fitur dan database sistem.</p>
    <p>Anda dapat mengelola keamanan basis data, mengatur struktur dan data, serta melakukan monitoring sistem.</p>
</div>

{{-- Notifikasi Pending Guru --}}
@if($pendingGuru > 0)
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <i class="fas fa-exclamation-circle" style="font-size: 2rem;"></i>
        <div style="flex-grow: 1;">
            <strong style="font-size: 1.1rem; display: block; margin-bottom: 0.25rem;">
                Ada {{ $pendingGuru }} guru menunggu verifikasi!
            </strong>
            <p style="margin: 0; opacity: 0.95;">
                Guru baru yang mendaftar memerlukan persetujuan Anda sebelum dapat login.
            </p>
        </div>
        <a href="{{ route('admin.verifikasi-guru') }}" class="btn" style="background: white; color: #764ba2; font-weight: 600; border: none; white-space: nowrap;">
            <i class="fas fa-user-check"></i> Verifikasi Sekarang
        </a>
    </div>
</div>
@endif

{{-- Notifikasi Pending Siswa - untuk siswa dengan NIS tidak terdaftar di data master --}}
@if($pendingSiswa > 0)
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <i class="fas fa-exclamation-circle" style="font-size: 2rem;"></i>
        <div style="flex-grow: 1;">
            <strong style="font-size: 1.1rem; display: block; margin-bottom: 0.25rem;">
                Ada {{ $pendingSiswa }} siswa menunggu verifikasi!
            </strong>
            <p style="margin: 0; opacity: 0.95;">
                Siswa dengan NIS tidak terdaftar di data master memerlukan persetujuan Anda.
            </p>
        </div>
        <a href="{{ route('admin.verifikasi-siswa') }}" class="btn" style="background: white; color: #dc2626; font-weight: 600; border: none; white-space: nowrap;">
            <i class="fas fa-user-check"></i> Verifikasi Sekarang
        </a>
    </div>
</div>
@endif

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value">{{ $totalUsers }}</div>
        <div class="stat-label">Total Pengguna</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-value">{{ $totalGuru }}</div>
        <div class="stat-label">Total Guru</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-value">{{ $totalSiswa }}</div>
        <div class="stat-label">Total Siswa</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-school"></i>
        </div>
        <div class="stat-value">{{ $totalKelas }}</div>
        <div class="stat-label">Total Kelas</div>
    </div>
</div>

<div class="content-section">
    <h3 class="section-title"><i class="fas fa-tasks"></i> Fitur Admin</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        
        {{-- Card Verifikasi Siswa Baru - untuk siswa dengan NIS tidak terdaftar di data master --}}
        @if($pendingSiswa > 0)
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; border-radius: 10px; min-height: 200px; display: flex; flex-direction: column; box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);">
            <h4 style="color: white; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-check"></i> Verifikasi Siswa Baru
            </h4>
            <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Ada <strong>{{ $pendingSiswa }} siswa</strong> dengan NIS tidak terdaftar yang menunggu persetujuan Anda.
            </p>
            <div>
                <a href="{{ route('admin.verifikasi-siswa') }}" class="btn" style="background: white; color: #dc2626; font-weight: 600; border: none;">
                    <i class="fas fa-clipboard-check"></i> Lihat & Verifikasi
                </a>
            </div>
        </div>
        @endif
        
        {{-- Card Verifikasi Guru --}}
        @if($pendingGuru > 0)
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; min-height: 200px; display: flex; flex-direction: column; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);">
            <h4 style="color: white; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-check"></i> Verifikasi Guru
            </h4>
            <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Ada <strong>{{ $pendingGuru }} guru</strong> yang menunggu persetujuan. Guru harus diverifikasi sebelum dapat login.
            </p>
            <div>
                <a href="{{ route('admin.verifikasi-guru') }}" class="btn" style="background: white; color: #764ba2; font-weight: 600; border: none;">
                    <i class="fas fa-clipboard-check"></i> Lihat & Verifikasi
                </a>
            </div>
        </div>
        @endif
        
        {{-- Card Verifikasi Guru - DISABLED (Guru langsung aktif tanpa approval)
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; min-height: 200px; display: flex; flex-direction: column; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);">
            <h4 style="color: white; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-check"></i> Verifikasi Guru
            </h4>
            <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Tinjau dan setujui pendaftaran guru baru. Guru yang belum disetujui tidak dapat login ke sistem.
            </p>
        </div>
        --}}

        <!-- 3. Kelola Struktur & Data -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #667eea; min-height: 200px; display: flex; flex-direction: column;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sitemap"></i> Kelola Struktur & Data
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Menambah, mengedit, menghapus data pengguna (kepala sekolah, pembina, guru, siswa)
            </p>
            <div>
                <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-cog"></i> Kelola Data
                </a>
            </div>
        </div>

        <!-- 4. Pengajuan Izin -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #667eea; min-height: 200px; display: flex; flex-direction: column;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-medical"></i> Pengajuan Izin
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Menyediakan dan mengatur fitur pengajuan izin digital (hadir, izin, sakit, alpha)
            </p>
            <div>
                <a href="{{ route('admin.pengajuan-izin') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-clipboard-list"></i> Lihat Izin
                </a>
            </div>
        </div>

        <!-- 5. Jadwal Pelajaran -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #667eea; min-height: 200px; display: flex; flex-direction: column;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-table"></i> Jadwal Pelajaran
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Mengatur dan membuat jadwal pelajaran (roster harian) untuk setiap kelas
            </p>
            <div>
                <a href="{{ route('admin.jadwal') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-calendar-alt"></i> Kelola Jadwal
                </a>
            </div>
        </div>

        <!-- 6. File Materi -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #667eea; min-height: 200px; display: flex; flex-direction: column;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-folder-open"></i> File Materi
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Memantau file materi pembelajaran yang diunggah guru sesuai ketentuan
            </p>
            <div>
                <a href="{{ route('admin.file-materi') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Lihat Materi
                </a>
            </div>
        </div>

        <!-- 7. Backup Database -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #667eea; min-height: 200px; display: flex; flex-direction: column;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd"></i> Backup Database
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem; flex-grow: 1;">
                Mengelola dan melakukan backup database secara berkala untuk keamanan data
            </p>
            <div>
                <a href="{{ route('admin.backup') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-download"></i> Kelola Backup
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content-section">
    <h3 class="section-title"><i class="fas fa-info-circle"></i> Informasi Sistem</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
        <div style="padding: 1rem; background: #f7fafc; border-radius: 8px;">
            <strong style="color: #4a5568;">Status Sistem:</strong>
            <span class="badge badge-success" style="margin-left: 0.5rem;">Active</span>
        </div>
        <div style="padding: 1rem; background: #f7fafc; border-radius: 8px;">
            <strong style="color: #4a5568;">Database:</strong>
            <span style="color: #2d3748; margin-left: 0.5rem;">MySQL Connected</span>
        </div>
        <div style="padding: 1rem; background: #f7fafc; border-radius: 8px;">
            <strong style="color: #4a5568;">Last Backup:</strong>
            <span style="color: #2d3748; margin-left: 0.5rem;">
                @if($lastBackup)
                    {{ $lastBackup->created_at->format('d M Y, H:i') }}
                @else
                    -
                @endif
            </span>
        </div>
        <div style="padding: 1rem; background: #f7fafc; border-radius: 8px;">
            <strong style="color: #4a5568;">Laravel Version:</strong>
            <span style="color: #2d3748; margin-left: 0.5rem;">{{ app()->version() }}</span>
        </div>
    </div>
</div>
@endsection
