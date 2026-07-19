@extends('layouts.dashboard')

@section('title', 'Pembina Dashboard')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-users"></i> Selamat Datang, {{ get_first_name() }}!</h2>
    <p>Halo <strong>{{ auth()->user()->nama_lengkap }}</strong>, selamat datang di dashboard Pembina.</p>
    <p>Anda dapat memantau kehadiran siswa, mengelola laporan aktivitas, dan mengawasi jadwal pembelajaran.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-value">{{ $totalJadwal }}</div>
        <div class="stat-label">Total Jadwal</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value">{{ $jadwalAktif }}</div>
        <div class="stat-label">Jadwal Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-value">{{ $totalLaporan }}</div>
        <div class="stat-label">Total Laporan</div>
    </div>
</div>

<div class="content-section">
    <h3 class="section-title"><i class="fas fa-tasks"></i> Fitur Pembina</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <!-- 1. Statistik Kehadiran -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-bar"></i> Statistik Kehadiran
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat statistik kehadiran siswa per kelas dan bulan
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.statistik-kehadiran') }}'">
                <i class="fas fa-chart-pie"></i> Lihat Statistik
            </button>
        </div>

        <!-- 2. Data Presensi -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clipboard-list"></i> Data Presensi
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Akses data presensi siswa dan guru (read-only)
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.presensi') }}'">
                <i class="fas fa-list"></i> Lihat Data
            </button>
        </div>

        <!-- 3. Jadwal Aktif -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-alt"></i> Jadwal Aktif
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat jadwal pembelajaran semua guru dan kelas
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.jadwal') }}'">
                <i class="fas fa-calendar"></i> Lihat Jadwal
            </button>
        </div>

        <!-- 4. Materi Pembelajaran -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-book"></i> Materi Pembelajaran
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat materi pembelajaran dari semua guru
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.materi') }}'">
                <i class="fas fa-eye"></i> Lihat Materi
            </button>
        </div>

        <!-- 5. Laporan Aktivitas -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-contract"></i> Laporan Aktivitas
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Mengelola laporan aktivitas siswa
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.laporan-aktivitas') }}'">
                <i class="fas fa-list"></i> Lihat Laporan
            </button>
        </div>

        <!-- 6. Kegiatan Sekolah -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-check"></i> Kegiatan Sekolah
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat jadwal dan informasi kegiatan sekolah
            </p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('pembina.kegiatan') }}'">
                <i class="fas fa-eye"></i> Lihat Kegiatan
            </button>
        </div>
    </div>
</div>

<style>
    .welcome-card {
        background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(3, 105, 161, 0.2);
    }

    .welcome-card h2 {
        margin: 0 0 0.5rem 0;
        font-size: 1.8rem;
    }

    .welcome-card p {
        margin: 0.5rem 0;
        font-size: 0.95rem;
        opacity: 0.95;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        text-align: center;
        border-top: 4px solid #0369a1;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        font-size: 2rem;
        color: #0369a1;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2d3748;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #718096;
        margin: 0;
    }

    .content-section {
        margin-top: 2rem;
    }

    .section-title {
        font-size: 1.3rem;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%);
        color: white;
        font-weight: 500;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.3);
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
</style>
@endsection