@extends('layouts.dashboard')

@section('title', 'Tambah Kegiatan')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-plus-circle"></i> Tambah Kegiatan Baru</h2>
    <p>Menambahkan kegiatan sekolah seperti rapat, ujian, atau acara resmi</p>
</div>

<div class="content-section">
    <form action="{{ route('admin.kegiatan.store') }}" method="POST">
        @csrf
        
        <div style="max-width: 800px;">
            <div class="form-group">
                <label for="nama_kegiatan">Nama Kegiatan <span style="color: #ef4444;">*</span></label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" class="form-control" required value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Rapat Dewan Guru">
                @error('nama_kegiatan')
                <small style="color: #ef4444;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="jenis_kegiatan">Jenis Kegiatan <span style="color: #ef4444;">*</span></label>
                <select id="jenis_kegiatan" name="jenis_kegiatan" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="rapat" {{ old('jenis_kegiatan') == 'rapat' ? 'selected' : '' }}>Rapat</option>
                    <option value="ujian" {{ old('jenis_kegiatan') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                    <option value="acara_resmi" {{ old('jenis_kegiatan') == 'acara_resmi' ? 'selected' : '' }}>Acara Resmi</option>
                    <option value="lainnya" {{ old('jenis_kegiatan') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('jenis_kegiatan')
                <small style="color: #ef4444;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="tanggal">Tanggal <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required value="{{ old('tanggal') }}">
                    @error('tanggal')
                    <small style="color: #ef4444;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="waktu_mulai">Waktu Mulai <span style="color: #ef4444;">*</span></label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" class="form-control" required value="{{ old('waktu_mulai') }}">
                    @error('waktu_mulai')
                    <small style="color: #ef4444;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="waktu_selesai">Waktu Selesai <span style="color: #ef4444;">*</span></label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" class="form-control" required value="{{ old('waktu_selesai') }}">
                    @error('waktu_selesai')
                    <small style="color: #ef4444;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Contoh: Ruang Guru">
                @error('lokasi')
                <small style="color: #ef4444;">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" placeholder="Tambahkan keterangan tambahan tentang kegiatan ini...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                <small style="color: #ef4444;">{{ $message }}</small>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Kegiatan
                </button>
                <a href="{{ route('admin.kegiatan') }}" class="btn" style="background: #6b7280; color: white;">
                    <i class="fas fa-arrow-left"></i> Batal
                </a>
            </div>
        </div>
    </form>
</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2d3748;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e0;
    border-radius: 8px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-row {
    display: flex;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
}
</style>
@endsection
