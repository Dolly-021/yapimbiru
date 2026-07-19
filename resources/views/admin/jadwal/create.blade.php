@extends('layouts.dashboard')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-plus-circle"></i> Tambah Jadwal Pelajaran</h2>
    <p>Menambahkan jadwal baru untuk kelas tertentu</p>
</div>

@if($errors->any())
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 8px;">
    <strong style="display: block; margin-bottom: 0.5rem;"><i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:</strong>
    <ul style="margin: 0; padding-left: 1.5rem;">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="content-section">
    <form action="{{ route('admin.jadwal.store') }}" method="POST" style="max-width: 800px;">
        @csrf
        
        <div style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1.5rem; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.75rem;">
                <i class="fas fa-info-circle"></i> Informasi Jadwal
            </h3>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    Hari <span style="color: #ef4444;">*</span>
                </label>
                <select name="hari" class="form-control" required>
                    <option value="">Pilih Hari</option>
                    <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                    <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                    <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    Kelas <span style="color: #ef4444;">*</span>
                </label>
                <select name="id_kelas" class="form-control" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} - {{ $k->jurusan }}
                    </option>
                    @endforeach
                </select>
                <small style="color: #64748b; margin-top: 0.25rem; display: block;">Pilih kelas yang akan mengikuti jadwal ini</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    Mata Pelajaran <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" placeholder="Contoh: Matematika, Bahasa Indonesia" required>
                <small style="color: #64748b; margin-top: 0.25rem; display: block;">Nama mata pelajaran yang akan diajarkan</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    Guru Pengajar <span style="color: #ef4444;">*</span>
                </label>
                <select name="id_guru" class="form-control" required>
                    <option value="">Pilih Guru</option>
                    @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->user->nama_lengkap }} - {{ $g->nip }}
                    </option>
                    @endforeach
                </select>
                <small style="color: #64748b; margin-top: 0.25rem; display: block;">Guru yang akan mengajar mata pelajaran ini</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                        Jam Mulai <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai') }}" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                        Jam Selesai <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai') }}" required>
                </div>
            </div>

            <div style="border-top: 2px solid #e2e8f0; padding-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.jadwal') }}" class="btn" style="background: #64748b; color: white;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Jadwal
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-control:disabled,
.form-control[readonly] {
    background-color: #f1f5f9;
    cursor: not-allowed;
}
</style>
@endsection
