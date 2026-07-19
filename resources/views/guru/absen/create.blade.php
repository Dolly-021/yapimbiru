@extends('layouts.dashboard')

@section('title', 'Buat Absen - ' . ($mapelName ?? 'N/A'))

@section('content')
<style>
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

    .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 991px) {
        .col-lg-8, .col-lg-4 {
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="container-fluid" style="padding: 2rem; max-width: 900px; margin: 0 auto;">
    <!-- Back Button -->
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('guru.absen.index') }}" class="btn btn-secondary" style="padding: 0.65rem 1.25rem; font-size: 0.95rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Header -->
    <div style="margin-bottom: 2.5rem;">
        <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 2.25rem; border-radius: 14px; box-shadow: 0 6px 16px rgba(59, 130, 246, 0.18);">
            <h2 style="font-weight: 600; color: white; margin-bottom: 1rem; font-size: 1.75rem; text-align: center;">
                <i class="fas fa-plus-circle" style="margin-right: 0.6rem;"></i>Buat Sesi Absen Baru
            </h2>
            <div style="text-align: center;">
                <span style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 0.65rem 1.25rem; border-radius: 10px; display: inline-block; font-size: 1rem; color: white;">
                    <i class="fas fa-book" style="margin-right: 0.4rem;"></i>
                    <strong>{{ $mapelName ?? 'N/A' }}</strong> 
                    <span style="margin: 0 0.6rem; opacity: 0.7;">•</span> 
                    <i class="fas fa-users" style="margin-right: 0.4rem;"></i>
                    Kelas <strong>{{ $jadwal->kelas->nama_kelas ?? 'N/A' }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="card border-0" style="border-radius: 14px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 650px; margin: 0 auto;">
        <!-- Section Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 1.25rem; color: white;">
            <h5 class="mb-0" style="font-weight: 600; font-size: 1.1rem; text-align: center;">
                <i class="fas fa-cog"></i>
                Pengaturan Sesi Absensi
            </h5>
        </div>

        <div class="card-body" style="padding: 2rem; background: white;">
            <form action="{{ route('guru.absen.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="mata_pelajaran" value="{{ $mapelName }}">
                <input type="hidden" name="id_kelas" value="{{ $idKelas }}">
                
                <!-- Jam Buka -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="color: #1f2937; font-weight: 600; margin-bottom: 0.5rem; display: block; font-size: 0.95rem;">
                        <i class="fas fa-clock" style="color: #3b82f6; margin-right: 0.4rem;"></i>
                        Jam Buka Absen
                    </label>
                    <input type="datetime-local" 
                           class="form-control @error('jam_buka') is-invalid @enderror" 
                           id="jam_buka" 
                           name="jam_buka"
                           value="{{ old('jam_buka') }}"
                           style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 0.7rem 0.9rem; font-size: 0.95rem; width: 100%;"
                           required>
                    @error('jam_buka')
                        <small class="text-danger mt-1" style="display: block; font-size: 0.85rem;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Jam Tutup -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="color: #1f2937; font-weight: 600; margin-bottom: 0.5rem; display: block; font-size: 0.95rem;">
                        <i class="fas fa-clock" style="color: #ef4444; margin-right: 0.4rem;"></i>
                        Jam Tutup Absen
                    </label>
                    <input type="datetime-local" 
                           class="form-control @error('jam_tutup') is-invalid @enderror" 
                           id="jam_tutup" 
                           name="jam_tutup"
                           value="{{ old('jam_tutup') }}"
                           style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 0.7rem 0.9rem; font-size: 0.95rem; width: 100%;"
                           required>
                    @error('jam_tutup')
                        <small class="text-danger mt-1" style="display: block; font-size: 0.85rem;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Topik Pembelajaran -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="color: #1f2937; font-weight: 600; margin-bottom: 0.5rem; display: block; font-size: 0.95rem;">
                        <i class="fas fa-graduation-cap" style="color: #10b981; margin-right: 0.4rem;"></i>
                        Topik Pembelajaran <span style="color: #9ca3af; font-weight: 400; font-size: 0.85rem;">(Opsional)</span>
                    </label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" 
                              name="keterangan"
                              rows="4"
                              placeholder=""
                              style="border-radius: 8px; border: 2px solid #e5e7eb; padding: 0.7rem 0.9rem; font-size: 0.95rem; resize: vertical; width: 100%; line-height: 1.5;">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <small class="text-danger mt-1" style="display: block; font-size: 0.85rem;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Action Button -->
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid #f3f4f6;">
                    <button type="submit" class="btn w-100"
                            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; font-weight: 600; padding: 0.8rem; border-radius: 8px; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle"></i>
                        Buat Sesi Absen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>@endsection
