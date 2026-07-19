@extends('layouts.dashboard')

@section('title', 'Statistik Kehadiran')

@section('content')
<div class="header-section" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1><i class="fas fa-chart-bar"></i> Statistik Kehadiran</h1>
        <p>Lihat statistik kehadiran siswa dan guru</p>
    </div>
    <x-dashboard-button />
</div>

<div class="filters-section" style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <form method="GET" class="filter-form">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <label>Bulan</label>
                <select name="bulan" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 6px;">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan', date('m')) == $i ? 'selected' : '' }}>
                            {{ ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$i-1] }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label>Tahun</label>
                <select name="tahun" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 6px;">
                    @for($i = date('Y') - 5; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.5rem;">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

<div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <h3 style="margin-bottom: 1.5rem; color: #2d3748;">Statistik Per Kelas</h3>
    
    @if($statistikKelas->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f7fafc; border-bottom: 2px solid #0369a1;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; color: #2d3748; font-weight: 600;">Kelas</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Total Siswa</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Hadir</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Izin</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Sakit</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Alfa</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">% Kehadiran</th>
                        <th style="padding: 1rem; text-align: center; color: #2d3748; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statistikKelas as $stat)
                        <tr style="border-bottom: 1px solid #e2e8f0; hover { background: #f7fafc; }">
                            <td style="padding: 1rem;">{{ $stat->nama_kelas ?? 'N/A' }}</td>
                            <td style="padding: 1rem; text-align: center;">{{ $stat->total_siswa_presensi ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: center; color: #22c55e; font-weight: 500;">{{ $stat->total_hadir ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: center; color: #eab308; font-weight: 500;">{{ $stat->total_izin ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: center; color: #f97316; font-weight: 500;">{{ $stat->total_sakit ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: center; color: #ef4444; font-weight: 500;">{{ $stat->total_alfa ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: center;">
                                <span style="background: #0369a1; color: white; padding: 0.25rem 0.75rem; border-radius: 15px;">
                                    {{ round($stat->persentase_kehadiran ?? 0, 2) }}%
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="{{ route('pembina.statistik-kehadiran.kelas', $stat->id_kelas ?? 0) }}" class="btn btn-sm btn-primary" style="padding: 0.4rem 0.8rem; text-decoration: none;">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 2rem; text-align: center; color: #718096;">
                                Tidak ada data statistik untuk bulan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div style="padding: 2rem; text-align: center; color: #718096; background: #f7fafc; border-radius: 8px;">
            <i class="fas fa-chart-bar" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
            <p>Tidak ada data statistik kehadiran</p>
        </div>
    @endif
</div>

<style>
    .header-section {
        margin-bottom: 2rem;
    }

    .header-section h1 {
        font-size: 1.8rem;
        color: #2d3748;
        margin: 0 0 0.5rem 0;
    }

    .header-section p {
        color: #718096;
        margin: 0;
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

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: #2d3748;
        font-weight: 500;
    }
</style>
@endsection
