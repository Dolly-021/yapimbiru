@extends('layouts.dashboard')

@section('title', 'Backup Database')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background: #64748b; color: white; padding: 0.75rem 1.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 6px;">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-hdd"></i> Backup Database</h2>
    <p>Kelola backup database sistem untuk keamanan data</p>
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

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-cloud-download-alt"></i> Buat Backup Baru</h3>
    </div>

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 12px; color: white; margin-bottom: 2rem;">
        <div style="display: grid; grid-template-columns: 1fr auto; gap: 2rem; align-items: center;">
            <div>
                <h4 style="margin: 0 0 0.5rem 0; font-size: 1.25rem;">
                    <i class="fas fa-database"></i> Backup Database MySQL (Custom Export)
                </h4>
                <p style="margin: 0; opacity: 0.9;">
                    Buat backup lengkap dari database <strong>{{ config('database.connections.mysql.database') }}</strong>
                </p>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.8; font-size: 0.9rem;">
                    ✓ Termasuk: Tables, Procedures, Functions, Triggers, Views & Events
                </p>
            </div>
            <div>
                <form action="{{ route('admin.backup.create') }}" method="POST" id="backupForm">
                    @csrf
                    <button type="submit" class="btn" style="background: white; color: #667eea; padding: 1rem 2rem; font-size: 1.1rem; font-weight: 600; border: none; cursor: pointer; border-radius: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <i class="fas fa-download"></i> Buat Backup Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-history"></i> Riwayat Backup</h3>
        <div style="color: #718096;">
            Total: <strong>{{ $backups->total() }}</strong> backup
        </div>
    </div>

    @if($backups->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama File</th>
                    <th style="width: 10%;">Ukuran</th>
                    <th style="width: 15%;">Dibuat Oleh</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 20%;">Keterangan</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($backups as $index => $backup)
                <tr>
                    <td style="text-align: center;">{{ $backups->firstItem() + $index }}</td>
                    <td>
                        <span style="font-family: monospace; font-size: 0.9rem; color: #2d3748;">
                            <i class="fas fa-file-code" style="color: #667eea;"></i> {{ $backup->nama_file }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: #e0e7ff; color: #4338ca; padding: 0.4rem 0.8rem;">
                            {{ format_file_size($backup->ukuran_file) }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-user-circle" style="color: #64748b;"></i>
                            <span>{{ $backup->user->nama ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.9rem;">
                            <div style="font-weight: 600; color: #2d3748;">
                                {{ $backup->created_at->format('d M Y') }}
                            </div>
                            <div style="color: #718096; font-size: 0.85rem;">
                                {{ $backup->created_at->format('H:i:s') }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="color: #64748b; font-size: 0.9rem;">
                            {{ $backup->keterangan }}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <a href="{{ route('admin.backup.download', $backup->id_backup) }}" 
                               class="btn btn-sm btn-primary" 
                               style="padding: 0.5rem 0.8rem;"
                               title="Download Backup">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <form action="{{ route('admin.backup.delete', $backup->id_backup) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Yakin ingin menghapus backup ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-danger" 
                                        style="padding: 0.5rem 0.8rem;"
                                        title="Hapus Backup">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $backups->links() }}
    </div>
    @else
    <div style="text-align: center; padding: 3rem; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-database" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
        <p style="margin: 0; font-size: 1.1rem;">Belum ada backup database</p>
        <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">Klik tombol "Buat Backup Sekarang" untuk membuat backup pertama</p>
    </div>
    @endif
</div>

<!-- Info & Tips -->
<div class="content-section">
    <h3 class="section-title"><i class="fas fa-info-circle"></i> Informasi Backup Custom</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <div style="padding: 1.5rem; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #10b981;">
            <h4 style="color: #065f46; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-check-circle"></i> Backup Lengkap
            </h4>
            <p style="color: #047857; margin: 0; font-size: 0.9rem;">
                Termasuk <strong>Stored Procedures, Functions, Triggers, Views & Events</strong> - sama seperti custom export phpMyAdmin
            </p>
        </div>
        <div style="padding: 1.5rem; background: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <h4 style="color: #1e40af; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database"></i> CREATE DATABASE
            </h4>
            <p style="color: #1e3a8a; margin: 0; font-size: 0.9rem;">
                File backup sudah include statement <code>CREATE DATABASE</code> dan <code>USE</code> - siap restore langsung
            </p>
        </div>
        <div style="padding: 1.5rem; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <h4 style="color: #92400e; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt"></i> Aman & Kompatibel
            </h4>
            <p style="color: #78350f; margin: 0; font-size: 0.9rem;">
                File menggunakan transaction, drop statements, dan format yang kompatibel dengan berbagai MySQL/MariaDB server
            </p>
        </div>
    </div>
</div>

<script>
// Disable button saat proses backup
document.getElementById('backupForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
});
</script>

<style>
.btn {
    transition: all 0.3s;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.btn-sm {
    font-size: 0.875rem;
}
.btn-primary {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn-primary:hover {
    background: #2563eb;
}
.btn-danger {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn-danger:hover {
    background: #dc2626;
}
</style>
@endsection
