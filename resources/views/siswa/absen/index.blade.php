@extends('layouts.dashboard')

@section('title', 'Isi Absen')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 class="mb-1" style="color: #3b82f6; font-weight: 700; font-size: 2rem;"><i class="fas fa-check-circle"></i> Isi Absen</h2>
                    <p class="text-muted">Pilih mata pelajaran untuk melihat dan mengisi absen</p>
                </div>
                <x-dashboard-button />
            </div>
        </div>
    </div>

    @if($mapels->isEmpty())
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> 
            <strong>Belum ada jadwal.</strong> Mata pelajaran Anda akan muncul di sini.
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            @foreach($mapels as $index => $mapel)
                <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; transition: all 0.3s ease; cursor: pointer;"
                     onmouseover="this.style.boxShadow='0 12px 24px rgba(0,0,0,0.15)'; this.style.transform='translateY(-4px)';"
                     onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)';">
                    
                    <!-- Header dengan Mata Pelajaran -->
                    <div style="padding: 1.5rem; text-align: center;">
                        <h3 style="color: #3b82f6; font-weight: 700; font-size: 1.25rem; margin: 0 0 0.5rem 0;">
                            {{ $mapel->mata_pelajaran ?? 'N/A' }}
                        </h3>
                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">
                            X-E1
                        </p>
                    </div>

                    <!-- Icon -->
                    <div style="text-align: center; padding: 1.5rem 0;">
                        <div style="width: 120px; height: 120px; margin: 0 auto; background: #fbbf24; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-book" style="font-size: 3rem; color: #10b981;"></i>
                        </div>
                    </div>

                    <!-- Button -->
                    <div style="padding: 1.5rem;">
                        <a href="{{ route('siswa.absen.show', $mapel->mata_pelajaran) }}" 
                           style="display: block; width: 100%; padding: 0.875rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s ease;"
                           onmouseover="this.style.boxShadow='0 4px 12px rgba(59, 130, 246, 0.4)'; this.style.transform='scale(1.02)';"
                           onmouseout="this.style.boxShadow='none'; this.style.transform='scale(1)';">
                            <i class="fas fa-book"></i> Isi Absen
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
        border-radius: 4px;
    }

    .card {
        border-radius: 8px;
    }

    .btn-primary {
        color: white;
        text-decoration: none;
    }

    .btn-primary:hover {
        color: white;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    .d-grid {
        display: grid;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .d-block {
        display: block;
    }
</style>
@endsection
