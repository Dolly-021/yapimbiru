@extends('layouts.dashboard')

@section('content')
<style>
    .page-title { font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem; }
    .page-desc { color: #718096; margin-bottom: 2rem; }
    .form-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 2rem; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; align-items: end; }
    .form-group { display: flex; flex-direction: column; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
    .form-control { padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 8px; font-size: 0.9rem; }
    .btn-primary { background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; }
    .btn-primary:hover { opacity: 0.9; }
    .btn-secondary { background: #e2e8f0; color: #2d3748; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; }
    .data-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
    .data-title { font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 1rem; }
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
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 2rem; }
    .info-item { background: white; padding: 1rem; border-radius: 8px; border-left: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; }
    .info-label { font-size: 0.8rem; color: #4a5568; font-weight: 600; margin-bottom: 0.5rem; }
    .info-value { font-size: 2rem; font-weight: 700; }
</style>

<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Header -->
    <div class="page-title">Rekap Presensi Bulanan</div>
    <div class="page-desc">Laporan ringkas kehadiran guru dan siswa</div>

    <!-- Filter Form -->
    <div class="form-card">
        <form method="GET" action="{{ route('kepala_sekolah.rekap-presensi') }}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-control">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromDate(2024, $i, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-control">
                        @for($year = date('Y') - 5; $year <= date('Y'); $year++)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-control">
                        <option value="guru" {{ $tipe == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ $tipe == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Tampilkan</button>
                <a href="{{ route('kepala_sekolah.rekap-presensi') }}" class="btn-secondary" style="text-decoration: none; line-height: 2.5; text-align: center;">Reset</a>
            </div>
        </form>
    </div>

    <!-- Rekap Table -->
    <div class="data-card">
        <div class="data-title">
            Rekap {{ $tipe == 'guru' ? 'Guru' : 'Siswa' }} - {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->format('F Y') }}
        </div>
        
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th style="text-align: center;">Hadir</th>
                        <th style="text-align: center;">Izin</th>
                        <th style="text-align: center;">Sakit</th>
                        <th style="text-align: center;">Alfa</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $data)
                        <tr>
                            <td>{{ $data->nama_lengkap ?? $data->nama ?? 'N/A' }}</td>
                            <td style="text-align: center;"><span class="badge badge-success">{{ $data->total_hadir ?? $data->hadir ?? 0 }}</span></td>
                            <td style="text-align: center;"><span class="badge badge-info">{{ $data->total_izin ?? $data->izin ?? 0 }}</span></td>
                            <td style="text-align: center;"><span class="badge badge-warning">{{ $data->total_sakit ?? $data->sakit ?? 0 }}</span></td>
                            <td style="text-align: center;"><span class="badge badge-danger">{{ $data->total_alpha ?? $data->alfa ?? 0 }}</span></td>
                            <td style="text-align: center; font-weight: 600;">{{ $data->total_hari ?? 0 }}</td>
                            <td style="text-align: center; font-weight: 600; color: {{ ($data->persentase ?? 0) >= 80 ? '#16a34a' : '#ea580c' }};">{{ $data->persentase ?? 0 }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #718096;">Tidak ada data rekap presensi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="info-grid">
        <div class="info-item" style="border-left-color: #16a34a;">
            <div class="info-label">✓ HADIR</div>
        </div>
        <div class="info-item" style="border-left-color: #2563eb;">
            <div class="info-label">I IZIN</div>
        </div>
        <div class="info-item" style="border-left-color: #ea580c;">
            <div class="info-label">S SAKIT</div>
        </div>
        <div class="info-item" style="border-left-color: #dc2626;">
            <div class="info-label">A ALFA</div>
        </div>
    </div>
</div>
@endsection