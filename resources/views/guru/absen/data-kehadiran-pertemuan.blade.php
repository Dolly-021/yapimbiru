@extends('layouts.dashboard')

@section('title', 'Data Kehadiran - ' . $mata_pelajaran)

@section('content')
<div style="padding: 2rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
            <a href="{{ route('guru.data-kehadiran') }}" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #6b7280; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease;"
               onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
               onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <x-dashboard-button />
        </div>
        
        <h2 style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">
            {{ $mata_pelajaran }}
        </h2>
        <p style="color: #6b7280; margin: 0; font-size: 0.9375rem;">Daftar pertemuan dan data kehadiran siswa</p>
    </div>

    <!-- Tabel Pertemuan -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        @if($absens->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 10%;">No</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 20%;">Tanggal</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 15%;">Jam</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 20%;">Topik</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 12%;">Total Siswa</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 23%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absens as $index => $absen)
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease;">
                                <!-- No -->
                                <td style="padding: 1rem; color: #1f2937; font-weight: 500; text-align: left;">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Tanggal -->
                                <td style="padding: 1rem; text-align: left;">
                                    <p style="margin: 0; font-weight: 600; color: #1f2937; font-size: 0.9375rem;">
                                        {{ $absen->jam_buka->format('d/m/Y') }}
                                    </p>
                                </td>

                                <!-- Jam -->
                                <td style="padding: 1rem; text-align: left;">
                                    <p style="margin: 0; color: #6b7280; font-size: 0.9375rem;">
                                        {{ $absen->jam_buka->format('H:i') }} - {{ $absen->jam_tutup->format('H:i') }}
                                    </p>
                                </td>

                                <!-- Topik -->
                                <td style="padding: 1rem; text-align: left;">
                                    <p style="margin: 0; color: #374151; font-size: 0.9375rem;">
                                        {{ $absen->keterangan ?? '-' }}
                                    </p>
                                </td>

                                <!-- Total Siswa -->
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.5rem 0.75rem; background-color: #dbeafe; color: #0c4a6e; border-radius: 0.375rem; font-weight: 600; font-size: 0.875rem;">
                                        {{ $absen->absenSiswas->count() }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <!-- Lihat/Edit -->
                                        <a href="{{ route('guru.absen.show', $absen->id) }}" 
                                           style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 0.375rem; text-decoration: none; font-weight: 600; font-size: 0.875rem; transition: all 0.2s ease;"
                                           onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.15)'"
                                           onmouseout="this.style.boxShadow='none'">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <!-- Hapus -->
                                        <form method="POST" action="{{ route('guru.absen.destroy', $absen->id) }}" style="display: inline;"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertemuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.75rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-weight: 600; font-size: 0.875rem; transition: all 0.2s ease;"
                                                    onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.15)'"
                                                    onmouseout="this.style.boxShadow='none'">
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
        @else
            <div style="padding: 3rem 1rem; text-align: center;">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #d1d5db; margin-bottom: 1rem;"></i>
                <p style="color: #9ca3af; font-size: 1.125rem;">Belum ada pertemuan untuk mata pelajaran ini</p>
            </div>
        @endif
    </div>
</div>

<style>
    tr:hover {
        background-color: #f9fafb;
    }

    @media (max-width: 768px) {
        table {
            font-size: 0.875rem;
        }

        td, th {
            padding: 0.75rem !important;
        }

        div[style*="display: flex; gap: 0.5rem"] {
            flex-direction: column !important;
        }

        a[style*="display: inline-flex"], button[style*="display: inline-flex"] {
            width: 100%;
        }
    }
</style>

@endsection
