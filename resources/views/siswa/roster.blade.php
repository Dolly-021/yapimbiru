@extends('layouts.dashboard')

@section('title', 'Roster Jadwal')

@section('content')
<div class="welcome-card" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2><i class="fas fa-calendar-alt"></i> Jadwal Mata Pelajaran</h2>
        @if($siswa && $siswa->kelas)
        <p style="margin-top: 0.5rem;">
            <strong>Kelas:</strong>
            <span style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 0%, #14b8a6 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.9rem;">
                {{ $siswa->kelas->nama_kelas }} - {{ $siswa->kelas->jurusan }}
            </span>
        </p>
        @endif
    </div>
    </a>
</div>

@if($jadwalPerHari->isEmpty())
<div class="content-section">
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p>Belum ada jadwal pelajaran yang tersedia untuk kelas Anda</p>
    </div>
</div>
@else
<div class="content-section">
    {{-- Tab Hari --}}
    <div style="background: white; border-radius: 8px; padding: 1rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem;">
            @php
                $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $hariAktif = request('hari', 'Senin');
                if (!in_array($hariAktif, $daftarHari) || !isset($jadwalPerHari[$hariAktif])) {
                    $hariAktif = $jadwalPerHari->keys()->first();
                }
            @endphp
            
            @foreach($daftarHari as $hari)
                @if(isset($jadwalPerHari[$hari]))
                <a href="?hari={{ $hari }}" 
                   style="padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 500; transition: all 0.2s ease;
                   @if($hariAktif === $hari)
                       background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white;
                   @else
                       background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
                   @endif
                   ">
                    {{ $hari }}
                </a>
                @endif
            @endforeach
        </div>

        {{-- Konten Jadwal Hari Aktif --}}
        @if(isset($jadwalPerHari[$hariAktif]))
            <div style="margin-top: 1.5rem;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tbody>
                            @foreach($jadwalPerHari[$hariAktif] as $jadwal)
                            <tr style="border-bottom: 1px solid #e2e8f0; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='#f8fafc';" onmouseout="this.style.backgroundColor='transparent';">
                                {{-- Mata Pelajaran & Guru --}}
                                <td style="padding: 1.25rem 1rem; text-align: left;">
                                    <div style="margin-bottom: 0.5rem;">
                                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 1rem;">
                                            {{ $jadwal->mata_pelajaran }}
                                        </p>
                                        <p style="margin: 0.25rem 0 0 0; color: #64748b; font-size: 0.875rem;">
                                            @if($jadwal->guru && $jadwal->guru->user)
                                                <i class="fas fa-user-circle" style="color: #0369a1; margin-right: 0.5rem;"></i>{{ $jadwal->guru->user->nama_lengkap }}
                                            @else
                                                <span style="color: #94a3b8; font-style: italic;">Guru belum ditentukan</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                {{-- Jam Pelajaran --}}
                                <td style="padding: 1.25rem 1rem; text-align: right; white-space: nowrap;">
                                    <span style="background: #f1f5f9; color: #0369a1; padding: 0.5rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.875rem;">
                                        <i class="fas fa-clock" style="margin-right: 0.5rem;"></i>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<div style="margin-top: 2rem;">
    <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<style>
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #64748b;
}

.empty-state i {
    font-size: 3rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1.1rem;
    color: #64748b;
}

@media (max-width: 768px) {
    table {
        font-size: 0.9rem;
    }
    
    th, td {
        padding: 0.75rem !important;
    }
    
    .section-title {
        font-size: 1rem;
    }
}
</style>
@endsection
