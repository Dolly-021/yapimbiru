@extends('layouts.dashboard')

@section('title', 'Detail User')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.users') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Users
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-user-circle"></i> Detail User</h2>
    <p>Informasi lengkap pengguna</p>
</div>

@if(session('success'))
<div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="content-section">
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; color: white;">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-circle" style="font-size: 5rem; color: #667eea;"></i>
                </div>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1.75rem;">{{ $user->nama_lengkap ?? $user->username }}</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        @php
                            $roleColors = [
                                'admin' => '#ef4444',
                                'kepala_sekolah' => '#8b5cf6',
                                'pembina' => '#3b82f6',
                                'guru' => '#10b981',
                                'siswa' => '#f59e0b'
                            ];
                            $color = $roleColors[$user->role] ?? '#64748b';
                        @endphp
                        <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-tag"></i> {{ str_replace('_', ' ', ucwords($user->role)) }}
                        </span>
                        <span style="background: {{ $user->status_aktif ? '#10b981' : '#ef4444' }}; padding: 0.5rem 1rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-{{ $user->status_aktif ? 'check-circle' : 'times-circle' }}"></i> 
                            {{ $user->status_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                        @if($user->status_approval)
                        <span style="background: rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-check"></i> {{ ucfirst($user->status_approval) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Dasar -->
        <div style="padding: 2rem;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-info-circle"></i> Informasi Dasar
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Username</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->username }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Nama Lengkap</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->nama_lengkap ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Email</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->email ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Terdaftar Sejak</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Detail Berdasarkan Role -->
        @if($user->role == 'guru' && $user->guru)
        <div style="padding: 0 2rem 2rem 2rem;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-chalkboard-teacher"></i> Detail Guru
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">NIP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->guru->nip ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">No. HP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->guru->no_hp ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Alamat</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->guru->alamat ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Jenis Kelamin</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->guru->jenis_kelamin ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Agama</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->guru->agama ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($user->role == 'siswa' && $user->siswa)
        <div style="padding: 0 2rem 2rem 2rem;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-user-graduate"></i> Detail Siswa
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">NIS</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->siswa->nis ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Kelas</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->siswa->kelas->nama_kelas ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Semester</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->siswa->semester ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">No. HP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->siswa->no_hp ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Alamat</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->siswa->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($user->role == 'kepala_sekolah' && $user->kepalaSekolah)
        <div style="padding: 0 2rem 2rem 2rem;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-user-tie"></i> Detail Kepala Sekolah
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">NIP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->kepalaSekolah->nip ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">No. HP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->kepalaSekolah->no_hp ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Alamat</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->kepalaSekolah->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($user->role == 'pembina' && $user->pembina)
        <div style="padding: 0 2rem 2rem 2rem;">
            <h4 style="color: #2d3748; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <i class="fas fa-user-shield"></i> Detail Pembina
            </h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">NIP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->pembina->nip ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">No. HP</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->pembina->no_hp ?? '-' }}</div>
                </div>
                <div>
                    <label style="display: block; color: #64748b; font-size: 0.9rem; margin-bottom: 0.5rem;">Alamat</label>
                    <div style="font-weight: 600; color: #2d3748;">{{ $user->pembina->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div style="padding: 0 2rem 2rem 2rem; display: flex; gap: 1rem;">
            <form action="{{ route('admin.users.toggle-status', $user->id_user) }}" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" class="btn" style="width: 100%; padding: 1rem; background: {{ $user->status_aktif ? '#ef4444' : '#10b981' }}; color: white; justify-content: center; font-size: 1rem;">
                    <i class="fas fa-{{ $user->status_aktif ? 'toggle-off' : 'toggle-on' }}"></i> 
                    {{ $user->status_aktif ? 'Nonaktifkan User' : 'Aktifkan User' }}
                </button>
            </form>
            @if($user->role != 'admin')
            <form action="{{ route('admin.users.delete', $user->id_user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="padding: 1rem 2rem; background: #64748b; color: white; font-size: 1rem;">
                    <i class="fas fa-trash"></i> Hapus User
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<style>
.btn {
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection
