@extends('layouts.dashboard')

@section('title', 'Riwayat Absen')

@section('content')
<div style="padding: 2rem;">
    <!-- Header Section -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.875rem; font-weight: 700; color: #1f2937; margin: 0 0 1rem 0;">Mahasiswa</h2>
        
        <!-- Tabel Header dengan Info -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                <div>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 500;">MATA PELAJARAN</p>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0;">
                        {{ $absen->keterangan ?? 'Tidak ada topik' }}
                    </h4>
                </div>
                <div>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 500;">KELAS</p>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0;">
                        {{ $absen->kelas->nama_kelas ?? 'N/A' }}
                    </h4>
                </div>
                <div>
                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 500;">JAM PERTEMUAN</p>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0;">
                        {{ $absen->jam_buka->format('H:i') }} - {{ $absen->jam_tutup->format('H:i') }}
                    </h4>
                </div>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('guru.absen.index') }}" style="display: inline-block; padding: 0.75rem 1.5rem; background-color: #6b7280; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease;"
                   onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                   onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <x-dashboard-button />
            </div>
        </div>
    </div>

    <!-- Tabel Absen -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <form id="absenForm" method="POST" action="{{ route('guru.absen.update', $absen->id) }}">
            @csrf
            @method('PUT')

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 8%;">No</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #374151; width: 42%;">Nama Nim</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 12.5%;">Hadir</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 12.5%;">Izin</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 12.5%;">Sakit</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #374151; width: 12.5%;">Absen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absenSiswas as $index => $absenSiswa)
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s ease;">
                                <!-- No -->
                                <td style="padding: 1rem; color: #1f2937; font-weight: 500; text-align: left;">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Nama Siswa & NIM -->
                                <td style="padding: 1rem; text-align: left;">
                                    <div>
                                        <p style="margin: 0; font-weight: 600; color: #1f2937; font-size: 0.9375rem;">
                                            {{ $absenSiswa->siswa?->user?->nama_lengkap ?? 'N/A' }}
                                        </p>
                                        <p style="margin: 0.25rem 0 0 0; color: #6b7280; font-size: 0.8125rem;">
                                            {{ $absenSiswa->siswa->nis ?? 'N/A' }}
                                        </p>
                                    </div>
                                </td>

                                <!-- Radio Hadir -->
                                <td style="padding: 1rem; text-align: center;">
                                    <input type="radio" 
                                           name="absen_status[{{ $absenSiswa->id }}]" 
                                           value="hadir"
                                           {{ $absenSiswa->status === 'hadir' ? 'checked' : '' }}
                                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #10b981;">
                                </td>

                                <!-- Radio Izin -->
                                <td style="padding: 1rem; text-align: center;">
                                    <input type="radio" 
                                           name="absen_status[{{ $absenSiswa->id }}]" 
                                           value="izin"
                                           {{ $absenSiswa->status === 'izin' ? 'checked' : '' }}
                                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #f59e0b;">
                                </td>

                                <!-- Radio Sakit -->
                                <td style="padding: 1rem; text-align: center;">
                                    <input type="radio" 
                                           name="absen_status[{{ $absenSiswa->id }}]" 
                                           value="sakit"
                                           {{ $absenSiswa->status === 'sakit' ? 'checked' : '' }}
                                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #ef4444;">
                                </td>

                                <!-- Radio Absen -->
                                <td style="padding: 1rem; text-align: center;">
                                    <input type="radio" 
                                           name="absen_status[{{ $absenSiswa->id }}]" 
                                           value="tidak_hadir"
                                           {{ $absenSiswa->status === 'tidak_hadir' ? 'checked' : '' }}
                                           style="width: 20px; height: 20px; cursor: pointer; accent-color: #8b5cf6;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Button Footer -->
            <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; text-align: right; background-color: #f9fafb;">
                <button type="button" 
                        onclick="document.getElementById('absenForm').reset()"
                        style="padding: 0.75rem 1.5rem; background-color: white; color: #374151; border: 1px solid #d1d5db; border-radius: 0.5rem; cursor: pointer; font-weight: 500; font-size: 0.9375rem; margin-right: 0.75rem; transition: all 0.2s ease;">
                    Reset
                </button>
                <button type="submit" 
                        style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; font-size: 0.9375rem; transition: all 0.2s ease;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    tr:hover {
        background-color: #f9fafb;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    @media (max-width: 768px) {
        div[style*="display: grid"] {
            grid-template-columns: 1fr !important;
        }

        table {
            font-size: 0.875rem;
        }

        td, th {
            padding: 0.75rem !important;
        }
    }
</style>

@endsection
