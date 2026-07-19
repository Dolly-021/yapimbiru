@extends('layouts.dashboard')

@section('title', 'Absen Siswa')

@section('content')
<style>
    .modal-header {
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    .btn-icon-circle {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
    }

    .modal-scrollable .modal-body {
        overflow-y: auto;
        max-height: calc(100vh - 200px);
    }
</style>

<div class="container-fluid" style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">
    <!-- Header Section with Back Button -->
    <div style="margin-bottom: 4rem;">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.65rem; margin-bottom: 2.5rem; padding: 0.95rem 1.6rem; border-radius: 10px; font-size: 1.05rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 3rem; border-radius: 16px; margin-bottom: 2.5rem; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);">
            <h2 class="mb-3" style="font-weight: 700; color: white; font-size: 2.15rem;">
                <i class="fas fa-clipboard-check" style="margin-right: 0.85rem;"></i>Kelola Absen Siswa
            </h2>
            <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin: 0;">Buat dan kelola sesi absensi untuk setiap mata pelajaran yang Anda ampu</p>
        </div>

        <a href="{{ route('guru.data-kehadiran') }}" 
           style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.65rem; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);"
           onmouseover="this.style.boxShadow='0 6px 16px rgba(245, 158, 11, 0.35)'; this.style.transform='translateY(-2px)';"
           onmouseout="this.style.boxShadow='0 4px 12px rgba(245, 158, 11, 0.25)'; this.style.transform='translateY(0)';">
            <i class="fas fa-chart-line"></i> Lihat Data Kehadiran
        </a>
    </div>

    @if($mapels->isEmpty())
        <div class="alert border-0" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 16px; padding: 3rem; text-align: center; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);">
            <div style="background: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);">
                <i class="fas fa-info-circle" style="font-size: 2.5rem; color: #3b82f6;"></i>
            </div>
            <h5 style="font-weight: 700; color: #1f2937; margin-bottom: 0.75rem; font-size: 1.25rem;">Belum ada mata pelajaran</h5>
            <p style="color: #6b7280; margin: 0; font-size: 1rem;">Anda tidak memiliki jadwal mengajar saat ini.</p>
        </div>
    @else
        <div class="row justify-content-center">
            @foreach($mapels as $index => $mapel)
                @php
                    // Warna gradient untuk setiap card
                    $gradients = [
                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                        'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                        'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                        'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                    ];
                    $gradient = $gradients[$index % count($gradients)];
                @endphp
                <div class="col-md-6 col-lg-6 mb-5">
                    <div class="card border-0 h-100" style="border-radius: 14px; overflow: hidden; display: flex; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                        <!-- Left: Content -->
                        <div style="flex: 1; padding: 2rem; background: white;">
                            <div style="margin-bottom: 1.25rem;">
                                <h5 style="font-weight: 600; color: #1f2937; margin-bottom: 0.65rem; font-size: 1.15rem; line-height: 1.4;">
                                    {{ $mapel->mata_pelajaran ?? 'N/A' }}
                                </h5>
                                <p class="mb-0" style="color: #6b7280; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-users" style="color: #3b82f6;"></i> 
                                    <span>Kelas {{ $mapel->kelas->nama_kelas ?? 'N/A' }}</span>
                                </p>
                            </div>

                            <!-- Guru Info -->
                            <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1.15rem; border-radius: 10px; margin-bottom: 1.25rem; border-left: 3px solid #3b82f6;">
                                <small style="color: #64748b; font-weight: 600; display: block; margin-bottom: 0.5rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.8px;">
                                    <i class="fas fa-chalkboard-teacher"></i> PENGAMPU
                                </small>
                                <p class="mb-0" style="color: #1e293b; font-weight: 600; font-size: 0.95rem;">
                                    {{ $mapel->guru->nama_lengkap ?? auth()->user()->nama_lengkap }}
                                </p>
                            </div>

                            <!-- Stats -->
                            <div class="row mb-4" style="gap: 0.75rem;">
                                <div class="col" style="background: #eff6ff; padding: 1.15rem; border-radius: 10px; text-align: center;">
                                    <small style="color: #60a5fa; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: block; margin-bottom: 0.5rem;">Pertemuan</small>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">
                                        {{ $mapel->total_absens ?? 0 }}
                                    </div>
                                </div>
                                <div class="col" style="background: #f0fdf4; padding: 1.15rem; border-radius: 10px; text-align: center;">
                                    <small style="color: #4ade80; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: block; margin-bottom: 0.5rem;">Jadwal</small>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">
                                        {{ $mapel->jadwal_count ?? 0 }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <a href="{{ route('guru.absen.create', ['mata_pelajaran' => $mapel->mata_pelajaran, 'id_kelas' => $mapel->id_kelas]) }}" 
                               class="btn btn-sm"
                               style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; font-weight: 600; padding: 0.8rem 1.25rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s ease; width: 100%; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); font-size: 0.95rem;"
                               onmouseover="this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.35)'; this.style.transform='translateY(-2px)';"
                               onmouseout="this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.25)'; this.style.transform='translateY(0)';">
                                <i class="fas fa-plus-circle"></i> Buat Sesi Absen
                            </a>
                        </div>

                        <!-- Right: Banner dengan Gradient -->
                        <div style="background: {{ $gradient }}; width: 120px; min-height: 200px; display: flex; align-items: center; justify-content: center; position: relative; flex-shrink: 0;">
                            <div style="position: absolute; opacity: 0.15; font-size: 5rem; color: white;">
                                <i class="fas fa-book"></i>
                            </div>
                            <div style="position: relative; text-align: center; color: white;">
                                <i class="fas fa-chalkboard" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>
                                <small style="font-weight: 600; font-size: 0.75rem;">{{ date('Y/m') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #475569 0%, #334155 100%);
        box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        transform: translateY(-2px);
        color: white;
    }

    @media (max-width: 768px) {
        .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

@endsection
