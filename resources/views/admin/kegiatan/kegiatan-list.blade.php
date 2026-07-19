@extends('layouts.dashboard')

@section('title', 'Kegiatan Sekolah')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-calendar-alt"></i> Kegiatan Sekolah</h2>
    <p>Mengelola data kegiatan sekolah seperti rapat, ujian, dan acara resmi</p>
</div>

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Kegiatan</h3>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Kegiatan
        </a>
    </div>

    @if($kegiatan->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Kegiatan</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 15%;">Lokasi</th>
                    <th style="width: 21%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan as $index => $item)
                <tr>
                    <td>{{ $kegiatan->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $item->nama_kegiatan }}</div>
                        @if($item->deskripsi)
                        <small style="color: #718096;">{{ Str::limit($item->deskripsi, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        @php
                            $jenisClass = [
                                'rapat' => 'badge-primary',
                                'ujian' => 'badge-warning',
                                'acara_resmi' => 'badge-success',
                                'lainnya' => 'badge-secondary'
                            ];
                        @endphp
                        <span class="badge {{ $jenisClass[$item->jenis_kegiatan] ?? 'badge-secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $item->jenis_kegiatan)) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        <small style="color: #4a5568;">
                            {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}
                        </small>
                    </td>
                    <td>{{ $item->lokasi ?? '-' }}</td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('admin.kegiatan.edit', $item->id_kegiatan) }}" class="btn btn-sm" style="background: #f59e0b; color: white; padding: 0.25rem 0.75rem;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.kegiatan.delete', $item->id_kegiatan) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white; padding: 0.25rem 0.75rem;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $kegiatan->links() }}
    </div>
    @else
    <div style="padding: 3rem; text-align: center; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-calendar-alt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p style="margin: 0 0 1rem 0; font-size: 1.1rem;">Belum ada kegiatan terdaftar</p>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Tambah Kegiatan Pertama
        </a>
    </div>
    @endif
</div>

<style>
.table-container {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s;
}

.data-table tbody tr:hover {
    background: #f7fafc;
}

.data-table td {
    padding: 1rem;
    font-size: 0.875rem;
    color: #2d3748;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-primary {
    background: #dbeafe;
    color: #1e40af;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
}
</style>
@endsection
