@extends('layouts.dashboard')

@section('title', 'Riwayat Pengajuan Izin')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('siswa.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-clipboard-list"></i> Riwayat Pengajuan Izin</h2>
    <p>Daftar lengkap semua pengajuan izin yang telah Anda buat</p>
</div>

@if(session('success'))
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Izin</h3>
        <a href="{{ route('siswa.izin.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajukan Izin Baru
        </a>
    </div>

    @if($izinList->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 1rem;">
        @foreach($izinList as $item)
        <div style="background: white; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; border-left: 4px solid {{ $item->status == 'disetujui' ? '#10b981' : ($item->status == 'ditolak' ? '#ef4444' : '#f59e0b') }};">
            <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem;">
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                        <h4 style="margin: 0; color: #1f2937; font-size: 1.1rem;">
                            {{ ucfirst($item->tipe) }}
                        </h4>
                        <span style="background: {{ $item->status == 'disetujui' ? '#d1fae5' : ($item->status == 'ditolak' ? '#fee2e2' : '#fef3c7') }}; color: {{ $item->status == 'disetujui' ? '#065f46' : ($item->status == 'ditolak' ? '#991b1b' : '#92400e') }}; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
                        <div>
                            <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 600;">Tanggal Izin</p>
                            <p style="margin: 0; color: #1f2937; font-weight: 500;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 600;">Hari</p>
                            <p style="margin: 0; color: #1f2937; font-weight: 500;">{{ $item->hari }}</p>
                        </div>
                        <div>
                            <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 600;">Tanggal Pengajuan</p>
                            <p style="margin: 0; color: #1f2937; font-weight: 500;">{{ $item->created_at->format('d F Y H:i') }}</p>
                        </div>
                    </div>

                    @if($item->alasan)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; color: #6b7280; font-weight: 600;">Alasan</p>
                        <p style="margin: 0; color: #475569; line-height: 1.5;">{{ $item->alasan }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($izinList->hasPages())
    <div style="margin-top: 2rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
        @if($izinList->onFirstPage())
            <span style="padding: 0.5rem 1rem; background: #e5e7eb; color: #9ca3af; border-radius: 6px; cursor: not-allowed;">
                <i class="fas fa-chevron-left"></i> Previous
            </span>
        @else
            <a href="{{ $izinList->previousPageUrl() }}" style="padding: 0.5rem 1rem; background: #667eea; color: white; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
        @endif

        @foreach($izinList->getUrlRange(1, $izinList->lastPage()) as $page => $url)
            @if($page == $izinList->currentPage())
                <span style="padding: 0.5rem 1rem; background: #667eea; color: white; border-radius: 6px; font-weight: 600;">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
                    {{ $page }}
                </a>
            @endif
        @endforeach

        @if($izinList->hasMorePages())
            <a href="{{ $izinList->nextPageUrl() }}" style="padding: 0.5rem 1rem; background: #667eea; color: white; text-decoration: none; border-radius: 6px; transition: all 0.2s;">
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
    <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 12px;">
        <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; color: #6b7280;"></i>
        <p style="color: #6b7280; font-size: 1.1rem; margin-bottom: 1rem;">Belum ada pengajuan izin</p>
        <a href="{{ route('siswa.izin.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus"></i> Ajukan Izin Pertama
        </a>
    </div>
    @endif
</div>

<style>
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
}

.welcome-card h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
}

.welcome-card p {
    margin: 0;
    opacity: 0.95;
    font-size: 0.95rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.content-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5568d3;
    transform: translateY(-2px);
}
</style>
@endsection
