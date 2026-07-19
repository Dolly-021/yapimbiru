@extends('layouts.dashboard')

@section('title', 'Kelas yang Diampu')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('guru.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-school"></i> Kelas yang Diampu</h2>
    <p>Daftar lengkap kelas dan jadwal pelajaran yang Anda ampu</p>
</div>

<div class="content-section">
    @if($kelasDetail && count($kelasDetail) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
            @foreach($kelasDetail as $kelas)
            <div style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                <!-- Header -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem;">
                    <h3 style="margin: 0; font-size: 1.375rem; margin-bottom: 0.5rem;">{{ $kelas['nama_kelas'] }}</h3>
                    <div style="display: flex; gap: 1rem; font-size: 0.9rem; opacity: 0.9;">
                        <span><i class="fas fa-users"></i> {{ $kelas['jumlah_siswa'] }} Siswa</span>
                        <span><i class="fas fa-calendar-alt"></i> {{ $kelas['jadwal']->count() }} Jadwal</span>
                    </div>
                </div>

                <!-- Jadwal List -->
                <div style="padding: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Jadwal Pelajaran</h4>
                    
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($kelas['jadwal'] as $j)
                        <div style="background: #f8fafc; padding: 0.75rem 1rem; border-radius: 8px; border-left: 3px solid #667eea;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 1rem;">
                                <div style="flex: 1;">
                                    <p style="margin: 0 0 0.25rem 0; color: #1f2937; font-weight: 600; font-size: 0.95rem;">
                                        {{ $j->mata_pelajaran }}
                                    </p>
                                    <p style="margin: 0 0 0.25rem 0; color: #6b7280; font-size: 0.85rem;">
                                        <i class="fas fa-calendar-day" style="width: 16px;"></i> {{ $j->hari }}
                                    </p>
                                    <p style="margin: 0; color: #6b7280; font-size: 0.85rem;">
                                        <i class="fas fa-clock" style="width: 16px;"></i> {{ $j->jam_mulai }} - {{ $j->jam_selesai }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Stats -->
                <div style="background: #f9fafb; padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; gap: 1rem;">
                    <div style="flex: 1; text-align: center;">
                        <p style="margin: 0 0 0.25rem 0; font-size: 0.75rem; text-transform: uppercase; color: #6b7280; font-weight: 600; letter-spacing: 0.5px;">Total Jam</p>
                        <p style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #1f2937;">{{ $kelas['jadwal']->count() }}</p>
                    </div>
                    <div style="flex: 1; text-align: center; border-left: 1px solid #e5e7eb;">
                        <p style="margin: 0 0 0.25rem 0; font-size: 0.75rem; text-transform: uppercase; color: #6b7280; font-weight: 600; letter-spacing: 0.5px;">Hari Ajar</p>
                        <p style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #1f2937;">{{ $kelas['jadwal']->unique('hari')->count() }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
    <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 12px;">
        <i class="fas fa-inbox" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3; color: #6b7280;"></i>
        <p style="color: #6b7280; font-size: 1.1rem; margin-bottom: 1rem;">Belum ada kelas yang Anda ampu</p>
        <p style="color: #9ca3af; font-size: 0.9rem;">Hubungi kepala sekolah untuk menambahkan jadwal Anda</p>
    </div>
    @endif
</div>

<style>
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
}

.welcome-card h2 {
    margin: 0 0 0.5rem 0;
    font-size: 1.75rem;
}

.welcome-card p {
    margin: 0;
    opacity: 0.95;
    font-size: 0.95rem;
}

.content-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
</style>
@endsection
