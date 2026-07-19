@extends('layouts.dashboard')

@section('content')
<style>
    .page-title { font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem; }
    .page-desc { color: #718096; margin-bottom: 2rem; }
    .form-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 2rem; max-width: 500px; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; }
    .form-group { display: flex; flex-direction: column; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
    .form-control { padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 8px; font-size: 0.9rem; }
    .btn-primary { width: 100%; background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .btn-primary:hover { opacity: 0.9; }
    .data-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
    .data-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .data-title { font-size: 1.1rem; font-weight: 700; color: #2d3748; }
    .data-subtitle { color: #718096; font-size: 0.9rem; margin-top: 0.25rem; }
    .btn-print { background: #4b5563; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; }
    .btn-print:hover { background: #374151; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .data-table thead { background: #f7fafc; }
    .data-table th { padding: 0.75rem; text-align: left; font-weight: 600; color: #4a5568; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; color: #2d3748; }
    .data-table tr:hover { background: #f7fafc; }
    .badge { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; }
    .badge-success { background: #c6f6d5; color: #22543d; }
    .badge-info { background: #bee3f8; color: #2c5282; }
    .badge-warning { background: #feebc8; color: #7c2d12; }
    .badge-danger { background: #fed7d7; color: #742a2a; }
    .btn-group { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; }
    .btn-action { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; }
    .btn-action-primary { background: #4b5563; color: white; }
    .btn-action-success { background: #16a34a; color: white; }
    .btn-action-primary:hover { background: #374151; }
    .btn-action-success:hover { background: #15803d; }
    .empty-state { background: #dbeafe; border-left: 4px solid #0369a1; padding: 2rem; border-radius: 8px; text-align: center; }
    .empty-state-text { color: #1e3a8a; font-weight: 600; }
</style>

<div style="max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div class="page-title">Download Rekap Presensi</div>
    <div class="page-desc">Generate dan download laporan presensi bulanan</div>

    <!-- Filter Form -->
    <div class="form-card">
        <form method="GET" action="{{ route('kepala_sekolah.download-rekap') }}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-control" required>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(2024, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-control" required>
                        @for($year = date('Y') - 5; $year <= date('Y'); $year++)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Filter Guru (Opsional)</label>
                    <select name="guru_id" class="form-control">
                        <option value="">-- Semua Guru --</option>
                        @forelse($daftarGuru as $guru)
                            <option value="{{ $guru->id_user }}" {{ $guruId == $guru->id_user ? 'selected' : '' }}>
                                {{ $guru->nama_lengkap }}
                            </option>
                        @empty
                            <option disabled>Tidak ada guru</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-download"></i> Generate Laporan
            </button>
        </form>
    </div>

    <!-- Rekap Preview -->
    @if($rekap && (count($rekap['guru']) > 0 || count($rekap['siswa']) > 0))
        <!-- Rekap Guru -->
        @if(count($rekap['guru']) > 0)
            <div class="data-card">
                <div class="data-header">
                    <div>
                        <div class="data-title">Rekap Presensi Guru - {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->format('F Y') }}</div>
                        <div class="data-subtitle">Total: {{ count($rekap['guru']) }} orang</div>
                    </div>
                    <button onclick="window.print()" class="btn-print">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th style="text-align: center;">Hadir</th>
                                <th style="text-align: center;">Izin</th>
                                <th style="text-align: center;">Sakit</th>
                                <th style="text-align: center;">Alfa</th>
                                <th style="text-align: center;">Total</th>
                                <th style="text-align: center;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekap['guru'] as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data->nama ?? 'N/A' }}</td>
                                    <td style="text-align: center;"><span class="badge badge-success">{{ $data->hadir ?? 0 }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-info">{{ $data->izin ?? 0 }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-warning">{{ $data->sakit ?? 0 }}</span></td>
                                    <td style="text-align: center;"><span class="badge badge-danger">{{ $data->alfa ?? 0 }}</span></td>
                                    <td style="text-align: center; font-weight: 600;">{{ ($data->hadir ?? 0) + ($data->izin ?? 0) + ($data->sakit ?? 0) + ($data->alfa ?? 0) }}</td>
                                    <td style="text-align: center; font-weight: 600; color: {{ ($data->persentase ?? 0) >= 80 ? '#16a34a' : '#ea580c' }};">{{ $data->persentase ?? 0 }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 2rem;">Belum ada data presensi untuk periode ini</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Export Options -->
        @if(count($rekap['guru']) > 0)
            <div class="data-card">
                <div class="btn-group">
                    <button onclick="window.print()" class="btn-action btn-action-primary">
                        <i class="fas fa-file-pdf"></i> Print PDF
                    </button>
                </div>
            </div>
        @endif
    @else
        <div class="empty-state">
            <p class="empty-state-text">Silakan pilih bulan dan tahun untuk melihat preview rekap presensi</p>
        </div>
    @endif
</div>
@endsection