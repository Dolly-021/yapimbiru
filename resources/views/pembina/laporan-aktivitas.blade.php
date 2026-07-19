@extends('layouts.dashboard')

@section('title', 'Laporan Aktivitas')

@section('content')
<style>
    .laporan-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .section-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .section-desc {
        color: #718096;
        margin-top: 0.25rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.3);
    }

    /* Table */
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .table-header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
        color: white;
    }

    .table-header h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    .laporan-table {
        width: 100%;
        border-collapse: collapse;
    }

    .laporan-table thead {
        background: #f8fafc;
    }

    .laporan-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.85rem;
    }

    .laporan-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }

    .laporan-table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-draft { background: #f3f4f6; color: #374151; }
    .badge-submitted { background: #fef3c7; color: #92400e; }
    .badge-reviewed { background: #dbeafe; color: #1e40af; }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .empty-state h3 {
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .pagination-wrapper {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .laporan-table {
            font-size: 0.8rem;
        }
        
        .laporan-table th,
        .laporan-table td {
            padding: 0.5rem;
        }
    }
</style>

<div class="laporan-wrapper">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h2 class="section-title"><i class="fas fa-file-contract"></i> Laporan Aktivitas</h2>
            <p class="section-desc">Daftar laporan aktivitas dari guru dan pembina</p>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Daftar Laporan</h3>
        </div>
        
        @if($laporan->count() > 0)
        <table class="laporan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Laporan</th>
                    <th>Pembuat</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $listBulan = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                @foreach($laporan as $index => $item)
                <tr>
                    <td>{{ $laporan->firstItem() + $index }}</td>
                    <td>
                        <strong>{{ $item->judul_laporan }}</strong>
                        @if($item->isi_laporan)
                            <p style="margin: 0.25rem 0 0 0; color: #64748b; font-size: 0.8rem;">
                                {{ Str::limit($item->isi_laporan, 80) }}
                            </p>
                        @endif
                    </td>
                    <td>
                        @if($item->pembina && $item->pembina->user)
                            {{ $item->pembina->user->nama_lengkap ?? $item->pembina->user->name }}
                            <span style="color: #64748b; font-size: 0.75rem;">(Pembina)</span>
                        @elseif($item->guru && $item->guru->user)
                            {{ $item->guru->user->nama_lengkap ?? $item->guru->user->name }}
                            <span style="color: #64748b; font-size: 0.75rem;">(Guru)</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $listBulan[$item->periode_bulan] ?? '-' }} {{ $item->periode_tahun }}</td>
                    <td>
                        <span class="badge badge-{{ $item->status }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($laporan->hasPages())
        <div class="pagination-wrapper">
            {{ $laporan->links() }}
        </div>
        @endif
        
        @else
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Data Laporan Belum Ada</h3>
            <p>Belum ada laporan aktivitas yang dibuat oleh guru atau pembina.</p>
        </div>
        @endif
    </div>
</div>

@endsection
