@extends('layouts.dashboard')

@section('title', 'Kegiatan Sekolah')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('pembina.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-calendar-alt"></i> Kegiatan Sekolah</h2>
    <p>Lihat jadwal kegiatan sekolah mendatang dan sedang berlangsung</p>
</div>

<!-- Filter Tabs -->
<div style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem;">
    <a href="{{ route('pembina.kegiatan', ['filter' => 'upcoming']) }}" 
       class="btn" 
       style="background: {{ $filter === 'upcoming' ? '#667eea' : '#e5e7eb' }}; 
               color: {{ $filter === 'upcoming' ? 'white' : '#374151' }}; 
               padding: 0.75rem 1.5rem; 
               text-decoration: none; 
               border-radius: 6px; 
               transition: all 0.2s;">
        <i class="fas fa-calendar"></i> Mendatang/Berlangsung
    </a>
    <a href="{{ route('pembina.kegiatan', ['filter' => 'history']) }}" 
       class="btn" 
       style="background: {{ $filter === 'history' ? '#667eea' : '#e5e7eb' }}; 
               color: {{ $filter === 'history' ? 'white' : '#374151' }}; 
               padding: 0.75rem 1.5rem; 
               text-decoration: none; 
               border-radius: 6px; 
               transition: all 0.2s;">
        <i class="fas fa-history"></i> Riwayat
    </a>
</div>

<div class="content-section">
    @if($kegiatan->count() > 0)
    <div class="kegiatan-grid">
        @foreach($kegiatan as $item)
        <div class="kegiatan-card">
            <div class="kegiatan-header" style="background: {{ $item->status == 'ongoing' ? '#fef3c7' : ($item->status == 'completed' ? '#d1fae5' : '#dbeafe') }};">
                <div style="flex: 1;">
                    <h3 style="margin: 0; color: #1f2937; font-size: 1.125rem;">{{ $item->nama_kegiatan }}</h3>
                    @if($item->status == 'ongoing')
                    <span class="badge badge-warning" style="margin-top: 0.5rem;"><i class="fas fa-circle" style="font-size: 0.5rem; animation: pulse 2s infinite;"></i> Sedang Berlangsung</span>
                    @elseif($item->status == 'completed')
                    <span class="badge badge-success" style="margin-top: 0.5rem;"><i class="fas fa-check-circle"></i> Selesai</span>
                    @else
                    <span class="badge badge-info" style="margin-top: 0.5rem;">Mendatang</span>
                    @endif
                </div>
            </div>
            <div class="kegiatan-body">
                <div class="info-row">
                    <i class="fas fa-calendar" style="color: #6b7280; width: 20px;"></i>
                    <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('l, d F Y') }}</span>
                </div>
                <div class="info-row">
                    <i class="fas fa-clock" style="color: #6b7280; width: 20px;"></i>
                    <span>{{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}</span>
                </div>
                @if($item->tempat)
                <div class="info-row">
                    <i class="fas fa-map-marker-alt" style="color: #6b7280; width: 20px;"></i>
                    <span>{{ $item->tempat }}</span>
                </div>
                @endif
                @if($item->deskripsi)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">{{ Str::limit($item->deskripsi, 100) }}</p>
                </div>
                @endif
            </div>
            <div class="kegiatan-footer">
                <a href="{{ route('pembina.kegiatan.detail', $item->id_kegiatan) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Detail
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if ($kegiatan->hasPages())
    <div style="margin-top: 2rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
        @if ($kegiatan->onFirstPage())
            <span style="padding: 0.5rem 1rem; background: #e5e7eb; color: #9ca3af; border-radius: 6px; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i> Previous
            </span>
        @else
            <a href="{{ $kegiatan->previousPageUrl() }}" style="padding: 0.5rem 1rem; background: #667eea; color: white; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        @endif

        @foreach ($kegiatan->getUrlRange(1, $kegiatan->lastPage()) as $page => $url)
            @if ($page == $kegiatan->currentPage())
                <span style="padding: 0.5rem 1rem; background: #667eea; color: white; border-radius: 6px; font-weight: 600;">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if ($kegiatan->hasMorePages())
            <a href="{{ $kegiatan->nextPageUrl() }}" style="padding: 0.5rem 1rem; background: #667eea; color: white; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        @else
            <span style="padding: 0.5rem 1rem; background: #e5e7eb; color: #9ca3af; border-radius: 6px; cursor: not-allowed;">
                Next <i class="fas fa-chevron-right"></i>
            </span>
        @endif
    </div>
    @endif
    @else
    <div style="text-align: center; padding: 3rem; color: #6b7280;">
        <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p>Tidak ada kegiatan yang dijadwalkan</p>
    </div>
    @endif
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.kegiatan-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.kegiatan-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.kegiatan-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.kegiatan-header {
    padding: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.kegiatan-body {
    padding: 1.5rem;
}

.kegiatan-footer {
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: #374151;
    font-size: 0.875rem;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-sm:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>
@endsection