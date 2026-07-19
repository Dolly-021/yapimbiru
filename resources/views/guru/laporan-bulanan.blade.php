@extends('layouts.dashboard')

@section('title', 'Laporan Bulanan')

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

    .filter-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        background: white;
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: #0369a1;
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

    .btn-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        text-align: center;
        border-left: 4px solid;
    }

    .stat-card.hadir { border-left-color: #10b981; }
    .stat-card.izin { border-left-color: #f59e0b; }
    .stat-card.sakit { border-left-color: #ef4444; }
    .stat-card.alpha { border-left-color: #6b7280; }
    .stat-card.terlambat { border-left-color: #8b5cf6; }
    .stat-card.persentase { border-left-color: #0369a1; }

    .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .stat-card.hadir .stat-icon { color: #10b981; }
    .stat-card.izin .stat-icon { color: #f59e0b; }
    .stat-card.sakit .stat-icon { color: #ef4444; }
    .stat-card.alpha .stat-icon { color: #6b7280; }
    .stat-card.terlambat .stat-icon { color: #8b5cf6; }
    .stat-card.persentase .stat-icon { color: #0369a1; }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-label {
        color: #64748b;
        font-size: 0.85rem;
        margin-top: 0.25rem;
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

    .presensi-table {
        width: 100%;
        border-collapse: collapse;
    }

    .presensi-table thead {
        background: #f8fafc;
    }

    .presensi-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.85rem;
    }

    .presensi-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }

    .presensi-table tbody tr:hover {
        background: #f8fafc;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-hadir { background: #d1fae5; color: #065f46; }
    .badge-izin { background: #fef3c7; color: #92400e; }
    .badge-sakit { background: #fee2e2; color: #991b1b; }
    .badge-alpha { background: #f3f4f6; color: #374151; }

    .badge-terlambat {
        background: #fae8ff;
        color: #7c3aed;
        font-size: 0.7rem;
        margin-left: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .filter-form {
            width: 100%;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="laporan-wrapper">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h2 class="section-title"><i class="fas fa-file-alt"></i> Laporan Bulanan</h2>
            <p class="section-desc">Rekap kehadiran Anda bulan {{ $listBulan[$bulan] }} {{ $tahun }}</p>
        </div>
        
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('guru.laporan-bulanan') }}" class="filter-form">
                <select name="bulan" class="filter-select">
                    @foreach($listBulan as $key => $nama)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
                <select name="tahun" class="filter-select">
                    @foreach($listTahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </form>
            
            <!-- Download Button -->
            <a href="{{ route('guru.laporan-bulanan.download', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               target="_blank" class="btn btn-success">
                <i class="fas fa-download"></i> Download / Print
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card hadir">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ $statistik['hadir'] }}</div>
            <div class="stat-label">Hari Hadir</div>
        </div>
        
        <div class="stat-card izin">
            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            <div class="stat-value">{{ $statistik['izin'] }}</div>
            <div class="stat-label">Izin</div>
        </div>
        
        <div class="stat-card sakit">
            <div class="stat-icon"><i class="fas fa-notes-medical"></i></div>
            <div class="stat-value">{{ $statistik['sakit'] }}</div>
            <div class="stat-label">Sakit</div>
        </div>
        
        <div class="stat-card alpha">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value">{{ $statistik['alpha'] }}</div>
            <div class="stat-label">Alpha</div>
        </div>
        
        <div class="stat-card terlambat">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-value">{{ $statistik['terlambat'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
        
        <div class="stat-card persentase">
            <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
            <div class="stat-value">{{ $statistik['persentase_kehadiran'] }}%</div>
            <div class="stat-label">Kehadiran</div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Detail Kehadiran Harian</h3>
        </div>
        
        @if($presensi->count() > 0)
        <table class="presensi-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($presensi as $index => $p)
                @php
                    $tanggal = \Carbon\Carbon::parse($p->tanggal);
                    $jamKerjaStandar = '07:30:00';
                    $isTerlambat = $p->jam_masuk && $p->jam_masuk > $jamKerjaStandar && $p->status == 'hadir';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tanggal->format('d M Y') }}</td>
                    <td>{{ $tanggal->locale('id')->dayName }}</td>
                    <td>
                        {{ $p->jam_masuk ?? '-' }}
                        @if($isTerlambat)
                            <span class="badge badge-terlambat">Terlambat</span>
                        @endif
                    </td>
                    <td>{{ $p->jam_keluar ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $p->status }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada data presensi untuk bulan {{ $listBulan[$bulan] }} {{ $tahun }}</p>
        </div>
        @endif
    </div>

    <!-- Summary Info -->
    <div style="margin-top: 1.5rem; padding: 1rem; background: #f0f9ff; border-radius: 8px; border-left: 4px solid #0369a1;">
        <p style="margin: 0; color: #0c4a6e;">
            <i class="fas fa-info-circle"></i>
            <strong>Info:</strong> Total hari kerja bulan {{ $listBulan[$bulan] }} {{ $tahun }} adalah {{ $statistik['hari_kerja'] }} hari (Senin-Sabtu).
            @if($statistik['total_menit_terlambat'] > 0)
                Total keterlambatan: {{ floor($statistik['total_menit_terlambat'] / 60) }} jam {{ $statistik['total_menit_terlambat'] % 60 }} menit.
            @endif
        </p>
    </div>
</div>

@endsection
