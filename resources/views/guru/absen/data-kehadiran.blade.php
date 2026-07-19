@extends('layouts.dashboard')

@section('title', 'Data Kehadiran')

@section('content')
<div style="padding: 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <a href="{{ route('guru.absen.index') }}" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #6b7280; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease;"
               onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <x-dashboard-button />
        </div>
        
        <h2 style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
            <i class="fas fa-clipboard-check" style="margin-right: 0.75rem;"></i>Data Kehadiran
        </h2>
        <p style="color: #6b7280; margin: 0; font-size: 0.9375rem;">Verifikasi dan input status kehadiran siswa</p>
    </div>

    <!-- Filter Section -->
    <div style="background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h3 style="font-weight: 700; color: #1f2937; margin-bottom: 1rem;">Filter Pertemuan</h3>
        
        <form method="GET" action="{{ route('guru.data-kehadiran') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; align-items: flex-end;">
            <!-- Filter Tanggal -->
            <div>
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <i class="fas fa-calendar-day"></i> Tanggal
                </label>
                <input type="date" name="tanggal" value="{{ $tanggalFilter }}" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.95rem;">
            </div>

            <!-- Filter Mata Pelajaran -->
            <div>
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <i class="fas fa-book"></i> Mata Pelajaran
                </label>
                <select name="mata_pelajaran" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.95rem;">
                    <option value="">-- Semua --</option>
                    @php
                        $mataPelajaran = $jadwal->pluck('mata_pelajaran')->unique();
                    @endphp
                    @foreach($mataPelajaran as $mapel)
                        <option value="{{ $mapel }}" {{ $mataPelajaranFilter == $mapel ? 'selected' : '' }}>
                            {{ $mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kelas -->
            <div>
                <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem;">
                    <i class="fas fa-graduation-cap"></i> Kelas
                </label>
                <select name="kelas" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.95rem;">
                    <option value="">-- Semua --</option>
                    @foreach($jadwal as $j)
                        <option value="{{ $j->id_kelas }}" {{ $kelasFilter == $j->id_kelas ? 'selected' : '' }}>
                            {{ $j->kelas->nama_kelas ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Button -->
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; font-size: 0.9rem; flex: 1;">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- List of Pertemuan -->
    @if($absens->count() > 0)
        <div style="display: grid; gap: 1rem;">
            @foreach($absens as $absen)
                <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; transition: all 0.2s ease;"
                     onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'; this.style.transform='translateY(-2px)';"
                     onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)';">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1.5rem; align-items: center;">
                        <!-- Info Pertemuan -->
                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 600; text-transform: uppercase;">Tanggal</p>
                            <p style="font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0;">
                                {{ $absen->jam_buka->format('d/m/Y') }}
                            </p>
                        </div>

                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 600; text-transform: uppercase;">Jam</p>
                            <p style="font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0;">
                                {{ $absen->jam_buka->format('H:i') }} - {{ $absen->jam_tutup->format('H:i') }}
                            </p>
                        </div>

                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.25rem 0; font-weight: 600; text-transform: uppercase;">Kelas</p>
                            <p style="font-size: 1rem; font-weight: 700; color: #1f2937; margin: 0;">
                                {{ $absen->kelas->nama_kelas ?? 'N/A' }}
                            </p>
                        </div>

                        <!-- Button -->
                        <a href="{{ route('guru.absen.show', $absen->id) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s ease;"
                           onmouseover="this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'; this.style.transform='translateY(-2px)';"
                           onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                            <i class="fas fa-edit"></i> Verifikasi
                        </a>
                    </div>

                    <!-- Keterangan -->
                    @if($absen->keterangan)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                                <strong>Topik:</strong> {{ $absen->keterangan }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div style="background: white; padding: 3rem 2rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
            <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block;"></i>
            <p style="color: #9ca3af; font-size: 1rem; margin: 0;">
                Tidak ada pertemuan yang sesuai dengan filter. Buatlah pertemuan baru atau ubah filter.
            </p>
            <a href="{{ route('guru.absen.create') }}" style="display: inline-block; margin-top: 1.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 600;">
                <i class="fas fa-plus"></i> Buat Pertemuan Baru
            </a>
        </div>
    @endif

    <!-- Divider -->
    <div style="margin: 3rem 0; text-align: center;">
        <div style="color: #d1d5db; font-size: 0.875rem; font-weight: 600;">ATAU</div>
    </div>

    <!-- Link ke Laporan Per Bulan -->
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%); padding: 1.5rem; border-radius: 0.75rem; border-left: 4px solid #f59e0b;">
        <div style="display: flex; align-items: center; gap: 1.5rem; justify-content: space-between;">
            <div>
                <h3 style="margin: 0 0 0.5rem 0; color: #92400e; font-size: 1rem; font-weight: 700;">
                    <i class="fas fa-file-pdf" style="margin-right: 0.5rem;"></i>Laporan Per Bulan Siswa
                </h3>
                <p style="margin: 0; color: #b45309; font-size: 0.9rem;">
                    Lihat rekapitulasi kehadiran bulanan dengan filter dan download PDF
                </p>
            </div>
            <a href="{{ route('guru.laporan-per-bulan-siswa') }}" 
               style="flex-shrink: 0; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; white-space: nowrap;">
                Akses Laporan <i class="fas fa-arrow-right" style="margin-left: 0.5rem;"></i>
            </a>
        </div>
    </div>
</div>

@endsection

