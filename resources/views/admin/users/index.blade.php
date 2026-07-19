@extends('layouts.dashboard')

@section('title', 'Manajemen Users')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-users-cog"></i> Manajemen Users</h2>
    <p>Kelola semua pengguna sistem (Kepala Sekolah, Pembina, Guru, Siswa)</p>
</div>

@if(session('success'))
<div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- Filter & Search -->
<div class="content-section">
    <form method="GET" action="{{ route('admin.users') }}" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500;">
                    <i class="fas fa-search"></i> Cari User
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Username, Nama, atau Email..." style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500;">
                    <i class="fas fa-user-tag"></i> Role
                </label>
                <select name="role" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <option value="">Semua Role</option>
                    <option value="kepala_sekolah" {{ request('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    <option value="pembina" {{ request('role') == 'pembina' ? 'selected' : '' }}>Pembina</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500;">
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; white-space: nowrap;">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.users') }}" class="btn" style="padding: 0.75rem 1rem; background: #64748b; color: white; text-decoration: none; display: inline-flex; align-items: center; border-radius: 6px;">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
<div class="content-section">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">Total Users</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ $users->total() }}</div>
        </div>
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">Kepala Sekolah</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ \App\Models\User::where('role', 'kepala_sekolah')->count() }}</div>
        </div>
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">Pembina</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ \App\Models\User::where('role', 'pembina')->count() }}</div>
        </div>
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 10px; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">Guru</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ \App\Models\User::where('role', 'guru')->count() }}</div>
        </div>
        <div style="padding: 1.5rem; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 10px; color: white;">
            <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 0.5rem;">Siswa</div>
            <div style="font-size: 2rem; font-weight: bold;">{{ \App\Models\User::where('role', 'siswa')->count() }}</div>
        </div>
    </div>
</div>

<!-- User List -->
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Users</h3>
    </div>

    @if($users->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Username</th>
                    <th style="width: 20%;">Nama Lengkap</th>
                    <th style="width: 15%;">Email</th>
                    <th style="width: 10%;">Role</th>
                    <th style="width: 10%;">Status Approval</th>
                    <th style="width: 10%;">Status Aktif</th>
                    <th style="width: 15%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                <tr>
                    <td style="text-align: center;">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-circle" style="color: #64748b; font-size: 1.5rem;"></i>
                            <strong>{{ $user->username }}</strong>
                        </div>
                    </td>
                    <td>{{ $user->nama_lengkap ?? '-' }}</td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td>
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
                        <span class="badge" style="background: {{ $color }}; color: white; padding: 0.4rem 0.8rem;">
                            {{ str_replace('_', ' ', ucfirst($user->role)) }}
                        </span>
                    </td>
                    <td>
                        @if($user->status_approval == 'approved')
                            <span class="badge" style="background: #d1fae5; color: #065f46; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-check-circle"></i> Approved
                            </span>
                        @elseif($user->status_approval == 'pending')
                            <span class="badge" style="background: #fef3c7; color: #92400e; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @elseif($user->status_approval == 'rejected')
                            <span class="badge" style="background: #fee2e2; color: #991b1b; padding: 0.4rem 0.8rem;">
                                <i class="fas fa-times-circle"></i> Rejected
                            </span>
                        @else
                            <span class="badge" style="background: #e2e8f0; color: #475569; padding: 0.4rem 0.8rem;">-</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.users.toggle-status', $user->id_user) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="badge" style="background: {{ $user->status_aktif ? '#10b981' : '#64748b' }}; color: white; padding: 0.4rem 0.8rem; border: none; cursor: pointer;">
                                <i class="fas fa-{{ $user->status_aktif ? 'toggle-on' : 'toggle-off' }}"></i> 
                                {{ $user->status_aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('admin.users.show', $user->id_user) }}" class="btn btn-sm" style="background: #3b82f6; color: white; padding: 0.5rem 0.8rem; text-decoration: none; border-radius: 6px;" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($user->role != 'admin')
                            <form action="{{ route('admin.users.delete', $user->id_user) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white; padding: 0.5rem 0.8rem; border: none; cursor: pointer; border-radius: 6px;" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        @if ($users->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; background: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="color: #64748b; font-size: 0.9rem;">
                Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} hasil
            </div>
            <div style="display: flex; gap: 0.5rem;">
                @if ($users->onFirstPage())
                    <span style="padding: 0.5rem 1rem; background: #f1f5f9; color: #94a3b8; border-radius: 6px; cursor: not-allowed;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" style="padding: 0.5rem 1rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                @endif

                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                        <span style="padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 6px; font-weight: 600; min-width: 40px; text-align: center;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding: 0.5rem 1rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; transition: all 0.2s; min-width: 40px; text-align: center;">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" style="padding: 0.5rem 1rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; transition: all 0.2s;">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span style="padding: 0.5rem 1rem; background: #f1f5f9; color: #94a3b8; border-radius: 6px; cursor: not-allowed;">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>
    @else
    <div style="text-align: center; padding: 3rem; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-users" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
        <p style="margin: 0; font-size: 1.1rem;">Tidak ada user ditemukan</p>
        <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">Coba ubah filter atau kata kunci pencarian</p>
    </div>
    @endif
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
.btn-primary {
    background: #3b82f6;
    color: white;
}
.btn-primary:hover {
    background: #2563eb;
}
.badge {
    display: inline-block;
    font-size: 0.85rem;
    font-weight: 600;
    border-radius: 6px;
}

/* Pagination link hover */
a[href*="page="]:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
    transform: translateY(-1px);
}
</style>
@endsection
