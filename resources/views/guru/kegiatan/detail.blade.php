@extends('layouts.dashboard')

@section('title', 'Detail Kegiatan')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('guru.kegiatan') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kegiatan
    </a>
</div>

<div class="detail-card">
    <div class="detail-header" style="background: {{ $kegiatan->status == 'ongoing' ? 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)' : 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' }};">
        <h1 style="color: white; margin: 0; font-size: 2rem;">{{ $kegiatan->nama_kegiatan }}</h1>
        @if($kegiatan->status == 'ongoing')
        <span class="badge" style="background: white; color: #92400e; margin-top: 1rem;">
            <i class="fas fa-circle" style="font-size: 0.5rem; animation: pulse 2s infinite;"></i> Sedang Berlangsung
        </span>
        @elseif($kegiatan->status == 'planned')
        <span class="badge" style="background: white; color: #1e40af; margin-top: 1rem;">Mendatang</span>
        @elseif($kegiatan->status == 'completed')
        <span class="badge" style="background: white; color: #065f46; margin-top: 1rem;">Selesai</span>
        @endif
    </div>

    <div class="detail-body">
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> Informasi Kegiatan</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">
                        <i class="fas fa-calendar"></i>
                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('l, d F Y') }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Waktu</div>
                    <div class="info-value">
                        <i class="fas fa-clock"></i>
                        {{ $kegiatan->waktu_mulai }} - {{ $kegiatan->waktu_selesai }}
                    </div>
                </div>
                @if($kegiatan->tempat)
                <div class="info-item">
                    <div class="info-label">Tempat</div>
                    <div class="info-value">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $kegiatan->tempat }}
                    </div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">Dibuat Oleh</div>
                    <div class="info-value">
                        <i class="fas fa-user"></i>
                        {{ $kegiatan->pembuatKegiatan->nama_lengkap ?? 'Tidak diketahui' }}
                    </div>
                </div>
            </div>
        </div>

        @if($kegiatan->deskripsi)
        <div class="info-section">
            <h3><i class="fas fa-file-alt"></i> Deskripsi</h3>
            <p style="color: #4b5563; line-height: 1.6;">{{ $kegiatan->deskripsi }}</p>
        </div>
        @endif
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.detail-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.detail-header {
    padding: 2.5rem;
}

.detail-body {
    padding: 2rem;
}

.info-section {
    margin-bottom: 2rem;
}

.info-section:last-child {
    margin-bottom: 0;
}

.info-section h3 {
    color: #1f2937;
    margin-bottom: 1.5rem;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    background: #f9fafb;
    padding: 1.25rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.info-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.info-value {
    color: #1f2937;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-value i {
    color: #667eea;
}

.badge {
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
@endsection