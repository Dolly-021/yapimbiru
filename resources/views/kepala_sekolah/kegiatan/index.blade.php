@extends('layouts.dashboard')

@section('title', 'Kegiatan Sekolah')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('kepala_sekolah.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-calendar-alt"></i> Kegiatan Sekolah</h2>
    <p>Kelola kegiatan sekolah seperti rapat, ujian, dan acara resmi</p>
</div>

@if(session('success'))
<div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('kepala_sekolah.kegiatan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Tambah Kegiatan Baru
    </a>
</div>

<div class="content-section">
    @if($kegiatan->count() > 0)
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Tempat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan as $index => $item)
                <tr>
                    <td>{{ $kegiatan->firstItem() + $index }}</td>
                    <td>
                        <strong>{{ $item->nama_kegiatan }}</strong>
                        @if($item->deskripsi)
                        <br><small style="color: #6b7280;">{{ Str::limit($item->deskripsi, 50) }}</small>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                    <td>
                        {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                    </td>
                    <td>{{ $item->tempat ?? '-' }}</td>
                    <td>
                        @if($item->status == 'planned')
                        <span class="badge badge-info">Mendatang</span>
                        @elseif($item->status == 'ongoing')
                        <span class="badge badge-warning">Sedang Berlangsung</span>
                        @elseif($item->status == 'completed')
                        <span class="badge badge-success">Selesai</span>
                        @else
                        <span class="badge badge-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            @if($item->status == 'planned')
                                <a href="{{ route('kepala_sekolah.kegiatan.edit', $item->id_kegiatan) }}" class="btn btn-sm" style="background: #f59e0b; color: white;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('kepala_sekolah.kegiatan.delete', $item->id_kegiatan) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-sm" style="background: #d1d5db; color: #6b7280; cursor: not-allowed;" disabled title="Tidak bisa diedit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm" style="background: #d1d5db; color: #6b7280; cursor: not-allowed;" disabled title="Tidak bisa dihapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
        <p>Belum ada kegiatan yang dijadwalkan</p>
        <a href="{{ route('kepala_sekolah.kegiatan.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus-circle"></i> Tambah Kegiatan Pertama
        </a>
    </div>
    @endif
</div>

<style>
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: #f3f4f6;
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.data-table tbody tr:hover {
    background: #f9fafb;
}

.btn-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-sm:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>
@endsection
