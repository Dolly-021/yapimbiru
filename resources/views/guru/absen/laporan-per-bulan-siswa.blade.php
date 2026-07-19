@extends('layouts.dashboard')

@section('title', 'Laporan Per Bulan Siswa')

@section('content')
<div style="padding: 2rem; max-width: 1400px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <div class="mb-4" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background-color: #6b7280; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease;"
           onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
           onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <x-dashboard-button />
    </div>

    <!-- Header -->
    <div class="mb-5">
        <h2 style="font-weight: 800; color: #1f2937; font-size: 2.5rem; margin-bottom: 1.5rem;">
            <i class="fas fa-chart-bar" style="margin-right: 0.75rem;"></i>Laporan Per Bulan Siswa
        </h2>
        <p style="color: #6b7280; margin: 0; font-size: 1rem;">Lihat riwayat kehadiran siswa per bulan dengan filter kelas</p>
    </div>

    <!-- Filter Section -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 2rem;">
        <h3 style="font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">
            <i class="fas fa-filter" style="margin-right: 0.5rem;"></i>Filter Data
        </h3>

        <form method="GET" action="{{ route('guru.laporan-per-bulan-siswa') }}" style="display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: flex-end;">
            <!-- Filter Bulan -->
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>Bulan
                </label>
                <input type="month" name="bulan" value="{{ $bulanFilter }}" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s ease;"
                       onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                       onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>

            <!-- Filter Kelas -->
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <i class="fas fa-graduation-cap" style="margin-right: 0.5rem;"></i>Kelas
                </label>
                <select name="kelas" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s ease;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ $kelasFilter == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Button Action -->
            <div style="display: flex; gap: 1rem;">
                <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);"
                        onmouseover="this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.3)'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.boxShadow='0 2px 8px rgba(59, 130, 246, 0.2)'; this.style.transform='translateY(0)';">
                    <i class="fas fa-search"></i> Terapkan Filter
                </button>

                <a href="{{ route('guru.laporan-per-bulan-siswa.download', ['bulan' => $bulanFilter, 'kelas' => $kelasFilter]) }}" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2); text-decoration: none;"
                   onmouseover="this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'; this.style.transform='translateY(-2px)';"
                   onmouseout="this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.2)'; this.style.transform='translateY(0)';">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Data Kehadiran -->
    @if($absens->count() > 0)
        <div style="background: white; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
            <!-- Summary Stats -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; padding: 2rem; background: linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%); border-bottom: 2px solid #e5e7eb;">
                <div style="text-align: center; padding: 1rem; background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <i class="fas fa-calendar-check" style="font-size: 2rem; color: #3b82f6; margin-bottom: 0.5rem; display: block;"></i>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0 0 0.5rem 0;">Total Pertemuan</p>
                    <p style="font-weight: 700; color: #1f2937; font-size: 1.75rem; margin: 0;">{{ $absens->count() }}</p>
                </div>

                <div style="text-align: center; padding: 1rem; background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <i class="fas fa-users" style="font-size: 2rem; color: #10b981; margin-bottom: 0.5rem; display: block;"></i>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0 0 0.5rem 0;">Total Siswa</p>
                    <p style="font-weight: 700; color: #1f2937; font-size: 1.75rem; margin: 0;">{{ $allSiswa->count() }}</p>
                </div>

                <div style="text-align: center; padding: 1rem; background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <i class="fas fa-check-circle" style="font-size: 2rem; color: #f59e0b; margin-bottom: 0.5rem; display: block;"></i>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0 0 0.5rem 0;">Rata-rata Kehadiran</p>
                    <p style="font-weight: 700; color: #1f2937; font-size: 1.75rem; margin: 0;">
                        @php
                            $totalHadir = 0;
                            $totalAbsen = 0;
                            foreach($absens as $absen) {
                                $totalHadir += $absen->absenSiswas->where('status', 'hadir')->count();
                                $totalAbsen += $absen->absenSiswas->count();
                            }
                            $rataRata = $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100, 1) : 0;
                        @endphp
                        {{ $rataRata }}%
                    </p>
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 1rem; text-align: left; font-weight: 700; color: #1f2937; font-size: 0.875rem;">No</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; color: #1f2937; font-size: 0.875rem;">Nama Siswa</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; color: #1f2937; font-size: 0.875rem;">Hadir</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; color: #1f2937; font-size: 0.875rem;">Izin</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; color: #1f2937; font-size: 0.875rem;">Sakit</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; color: #1f2937; font-size: 0.875rem;">Tidak Hadir</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; color: #1f2937; font-size: 0.875rem;">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Count attendance per student
                            $siswaStats = [];
                            foreach($allSiswa as $siswa) {
                                $siswaStats[$siswa->id_siswa] = [
                                    'hadir' => 0,
                                    'izin' => 0,
                                    'sakit' => 0,
                                    'tidak_hadir' => 0
                                ];
                                
                                foreach($absens as $absen) {
                                    $absenSiswa = $absen->absenSiswas->where('id_siswa', $siswa->id_siswa)->first();
                                    if($absenSiswa) {
                                        $siswaStats[$siswa->id_siswa][$absenSiswa->status]++;
                                    }
                                }
                            }
                        @endphp
                        
                        @forelse($allSiswa as $idx => $siswa)
                            @php
                                $totalSiswa = array_sum($siswaStats[$siswa->id_siswa]);
                                $persenHadir = $totalSiswa > 0 ? round(($siswaStats[$siswa->id_siswa]['hadir'] / $totalSiswa) * 100, 1) : 0;
                            @endphp
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease;"
                                onmouseover="this.style.backgroundColor='#f9fafb';"
                                onmouseout="this.style.backgroundColor='white';">
                                <td style="padding: 1rem; color: #1f2937; font-weight: 500; text-align: center;">{{ $idx + 1 }}</td>
                                <td style="padding: 1rem; color: #1f2937; font-weight: 500;">{{ $siswa->user?->name ?? 'N/A' }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $siswaStats[$siswa->id_siswa]['hadir'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $siswaStats[$siswa->id_siswa]['izin'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #fecaca; color: #7f1d1d; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $siswaStats[$siswa->id_siswa]['sakit'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #e5e7eb; color: #374151; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $siswaStats[$siswa->id_siswa]['tidak_hadir'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $persenHadir }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 1rem; text-align: center; color: #999;">Tidak ada data siswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div style="background: white; padding: 3rem 2rem; border-radius: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center;">
            <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
            <p style="color: #9ca3af; font-size: 1.125rem; margin: 0;">
                Tidak ada data kehadiran untuk periode bulan {{ date('F Y', strtotime($bulanFilter)) }}
                @if($kelasFilter)
                    di kelas terpilih
                @endif
            </p>
        </div>
    @endif
</div>

@endsection
