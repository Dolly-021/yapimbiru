@extends('layouts.dashboard')

@section('title', 'Isi Absen')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('siswa.absen.show', $absen->guru_kelas_mapel_id) }}" class="btn btn-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h2><i class="fas fa-check-square"></i> Isi Absen</h2>
            <p class="text-muted">
                Tanggal: <strong>{{ now()->format('d M Y H:i') }}</strong>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-check"></i> Konfirmasi Kehadiran</h5>
                </div>
                <div class="card-body">
                    @php
                        $now = now();
                        $isOpen = $now >= $absen->jam_buka && $now <= $absen->jam_tutup;
                    @endphp

                    @if(!$isOpen)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Waktu absen sudah ditutup!</strong> Absen hanya dapat diisi antara 
                            {{ $absen->jam_buka->format('H:i') }} - {{ $absen->jam_tutup->format('H:i') }}
                        </div>
                    @else
                        <form action="{{ route('siswa.absen.store') }}" method="POST">
                            @csrf
                            
                            <input type="hidden" name="absen_id" value="{{ $absen->id }}">

                            <div class="form-group mb-4">
                                <label for="status" class="form-label">
                                    <i class="fas fa-info-circle"></i> Status Kehadiran <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group btn-group-toggle d-flex flex-wrap gap-2" role="group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status_hadir" value="hadir" checked>
                                        <label class="form-check-label btn btn-outline-success" for="status_hadir">
                                            <i class="fas fa-check-circle"></i> Hadir
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status_izin" value="izin">
                                        <label class="form-check-label btn btn-outline-warning" for="status_izin">
                                            <i class="fas fa-ban"></i> Izin
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="status_sakit" value="sakit">
                                        <label class="form-check-label btn btn-outline-info" for="status_sakit">
                                            <i class="fas fa-hospital"></i> Sakit
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="keterangan" class="form-label">
                                    <i class="fas fa-comment"></i> Keterangan (Opsional)
                                </label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                          id="keterangan" 
                                          name="keterangan"
                                          rows="3"
                                          placeholder="Masukkan keterangan jika diperlukan...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Contoh: Tidak sehat, Dokter, dll</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-clock"></i> 
                                <strong>Waktu Tersisa:</strong> 
                                <span id="countdown">Menghitung...</span>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save"></i> Simpan Absen
                                </button>
                                <a href="{{ route('siswa.absen.show', $absen->guru_kelas_mapel_id) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Absen</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <small class="text-muted">Mata Pelajaran</small>
                    </p>
                    <p class="mb-3">
                        <strong>{{ $absen->guruKelasMapel->mata_pelajaran ?? 'N/A' }}</strong>
                    </p>

                    <p class="mb-2">
                        <small class="text-muted">Guru Pengampu</small>
                    </p>
                    <p class="mb-3">
                        <strong>{{ $absen->guru?->user?->nama_lengkap ?? 'N/A' }}</strong>
                    </p>

                    <p class="mb-2">
                        <small class="text-muted">Jam Buka</small>
                    </p>
                    <p class="mb-3">
                        <strong>{{ $absen->jam_buka->format('d M Y H:i') }}</strong>
                    </p>

                    <p class="mb-2">
                        <small class="text-muted">Jam Tutup</small>
                    </p>
                    <p class="mb-3">
                        <strong>{{ $absen->jam_tutup->format('d M Y H:i') }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .gap-2 {
        gap: 0.5rem;
    }
    
    .d-flex {
        display: flex;
    }
    
    .btn-group-toggle .form-check {
        flex: 1;
    }
    
    .btn-group-toggle .form-check-input:checked ~ .form-check-label {
        border-color: #495057;
        background-color: #495057;
        color: white;
    }
    
    .btn-outline-success:checked,
    .form-check-input:checked ~ .btn-outline-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
</style>

<script>
    function updateCountdown() {
        const jamTutup = new Date('{{ $absen->jam_tutup->format('Y-m-d H:i:s') }}');
        const now = new Date();
        const diff = jamTutup - now;

        if (diff <= 0) {
            document.getElementById('countdown').innerText = 'Waktu absen sudah habis!';
            return;
        }

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        document.getElementById('countdown').innerText = 
            `${hours} jam, ${minutes} menit, ${seconds} detik`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endsection
