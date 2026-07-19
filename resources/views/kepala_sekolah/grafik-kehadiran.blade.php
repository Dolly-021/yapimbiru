@extends('layouts.dashboard')

@section('content')
<style>
    .page-title { font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem; }
    .page-desc { color: #718096; margin-bottom: 2rem; }
    .form-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 2rem; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end; }
    .form-group { display: flex; flex-direction: column; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
    .form-control { padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 8px; font-size: 0.9rem; }
    .btn-primary { background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(3, 105, 161, 0.3); }
    .btn-secondary { background: #e2e8f0; color: #2d3748; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: #cbd5e0; }
    .data-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
    .data-title { font-size: 1.1rem; font-weight: 700; color: #2d3748; margin-bottom: 1rem; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead { background: #f7fafc; }
    .data-table th { padding: 0.75rem; text-align: left; font-weight: 600; color: #4a5568; border-bottom: 2px solid #e2e8f0; }
    .data-table td { padding: 0.75rem; border-bottom: 1px solid #e2e8f0; color: #2d3748; }
    .data-table tr:hover { background: #f7fafc; }
    .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .badge-success { background: #c6f6d5; color: #22543d; }
    .info-box { background: #dbeafe; border-left: 4px solid #2563eb; padding: 1rem; border-radius: 8px; margin-top: 2rem; }
    .info-title { font-weight: 700; color: #1e40af; margin-bottom: 0.5rem; }
    .info-text { color: #1e3a8a; }
</style>

<div style="max-width: 1200px; margin: 0 auto;">
    <!-- Header -->
    <div class="page-title">Grafik Kehadiran</div>
    <div class="page-desc">Analisis tren kehadiran guru dan siswa</div>

    <!-- Filter Form -->
    <div class="form-card">
        <form method="GET" action="{{ route('kepala_sekolah.grafik-kehadiran') }}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Filter Kelas Siswa (Opsional)</label>
                    <select name="kelas_id" class="form-control">
                        <option value="">-- Semua Kelas --</option>
                        @forelse($daftarKelas as $kelas)
                            <option value="{{ $kelas->id_kelas }}" {{ $kelasId == $kelas->id_kelas ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @empty
                            <option disabled>Tidak ada kelas</option>
                        @endforelse
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="{{ route('kepala_sekolah.grafik-kehadiran') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        <!-- Grafik Kehadiran Guru -->
        <div class="data-card">
            <div class="data-title">Kehadiran Guru</div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th style="text-align: right;">Hadir</th>
                            <th style="text-align: right;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grafikGuru as $data)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}</td>
                                <td style="text-align: right; font-weight: 600;">{{ $data->jumlah_hadir ?? 0 }}</td>
                                <td style="text-align: right;">
                                    <span class="badge badge-success">{{ $data->persentase ?? 0 }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 2rem; color: #718096;">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grafik Kehadiran Siswa Per Kelas -->
        @php
            $grafikByKelas = $grafikSiswa->groupBy('nama_kelas');
        @endphp
        
        @if(count($grafikSiswa) > 0)
            @foreach($grafikByKelas as $namaKelas => $dataKelas)
                <div class="data-card">
                    <div class="data-title">Kehadiran Siswa - {{ $namaKelas }}</div>
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th style="text-align: right;">Hadir</th>
                                    <th style="text-align: right;">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dataKelas as $data)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}</td>
                                        <td style="text-align: right; font-weight: 600;">{{ $data->jumlah_hadir ?? 0 }}</td>
                                        <td style="text-align: right;">
                                            <span class="badge badge-success">{{ $data->persentase ?? 0 }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; padding: 2rem; color: #718096;">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="data-card">
                <div class="data-title">Kehadiran Siswa</div>
                <div style="text-align: center; padding: 2rem; color: #718096;">Tidak ada data presensi siswa untuk periode ini</div>
            </div>
        @endif
    </div>

    <!-- Summary -->
    <div class="info-box">
        <div class="info-title">Periode Laporan</div>
        <div class="info-text">
            {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </div>
    </div>
</div>
@endsection