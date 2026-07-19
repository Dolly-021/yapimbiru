@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran Siswa Baru')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;">
            <i class="fas fa-user-check"></i> Verifikasi Pendaftaran Siswa Baru
        </h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div style="background: #e3f2fd; color: #1565c0; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #2196f3;">
        <i class="fas fa-info-circle"></i> 
        <strong>Info:</strong> Siswa yang ditampilkan di bawah adalah yang mendaftar dengan NIS yang <strong>TIDAK TERDAFTAR</strong> di data master. 
        Siswa dengan NIS terdaftar akan langsung aktif tanpa perlu approval.
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #28a745;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #dc3545;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @if($pendingSiswa->count() > 0)
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white;">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">No</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">NIS</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Nama Lengkap</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Email</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Kelas</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Tanggal Daftar</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingSiswa as $index => $user)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 1rem;">{{ $index + 1 }}</td>
                    <td style="padding: 1rem; font-family: monospace; font-weight: 500;">{{ $user->siswa->nis ?? '-' }}</td>
                    <td style="padding: 1rem; font-weight: 500;">{{ $user->nama_lengkap }}</td>
                    <td style="padding: 1rem;">{{ $user->email }}</td>
                    <td style="padding: 1rem;">
                        @if($user->siswa && $user->siswa->kelas)
                            <span class="badge badge-info">{{ $user->siswa->kelas->nama_kelas }}</span>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td style="padding: 1rem;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 1rem; text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <button 
                                onclick="approveSiswa({{ $user->id_user }}, '{{ $user->nama_lengkap }}')" 
                                class="btn btn-success btn-sm" 
                                style="min-width: 45px; padding: 0.5rem;"
                                title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button 
                                onclick="rejectSiswa({{ $user->id_user }}, '{{ $user->nama_lengkap }}')" 
                                class="btn btn-danger btn-sm"
                                style="min-width: 45px; padding: 0.5rem;"
                                title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <i class="fas fa-inbox" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
        <p style="color: #718096; font-size: 1.1rem;">Tidak ada pendaftaran siswa yang menunggu verifikasi</p>
        <p style="color: #a0aec0; font-size: 0.9rem; margin-top: 0.5rem;">
            Semua siswa baru sudah disetujui atau mendaftar dengan NIS yang terdaftar di data master.
        </p>
    </div>
    @endif
</div>

<style>
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

table tbody tr:hover {
    background-color: #f7fafc;
}

.btn-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.btn-success:hover {
    background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
}

.badge-info {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
}
</style>

<script>
function approveSiswa(userId, namaSiswa) {
    Swal.fire({
        title: 'Approve Pendaftaran?',
        html: `Anda akan menyetujui pendaftaran siswa:<br><strong>${namaSiswa}</strong><br><br>Siswa akan dapat login ke sistem setelah diapprove.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Ya, Approve',
        cancelButtonText: '<i class="fas fa-times"></i> Batal',
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#718096'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get CSRF token dari meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Submit form approve menggunakan fetch
            fetch(`/admin/verifikasi-siswa/${userId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Pendaftaran siswa ${namaSiswa} berhasil disetujui.`,
                        icon: 'success',
                        confirmButtonColor: '#48bb78'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menyetujui pendaftaran siswa.',
                        icon: 'error',
                        confirmButtonColor: '#f56565'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#f56565'
                });
            });
        }
    });
}

function rejectSiswa(userId, namaSiswa) {
    Swal.fire({
        title: 'Reject Pendaftaran?',
        html: `Anda akan menolak pendaftaran siswa:<br><strong>${namaSiswa}</strong><br><br>Akun akan dihapus secara permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Ya, Reject',
        cancelButtonText: '<i class="fas fa-ban"></i> Batal',
        confirmButtonColor: '#f56565',
        cancelButtonColor: '#718096',
        input: 'textarea',
        inputPlaceholder: 'Alasan penolakan (opsional)',
        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Get CSRF token dari meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Submit form reject menggunakan fetch
            fetch(`/admin/verifikasi-siswa/${userId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    alasan: result.value || ''
                })
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Pendaftaran siswa ${namaSiswa} berhasil ditolak dan dihapus.`,
                        icon: 'success',
                        confirmButtonColor: '#48bb78'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Gagal menolak pendaftaran siswa.',
                        icon: 'error',
                        confirmButtonColor: '#f56565'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#f56565'
                });
            });
        }
    });
}
</script>
@endsection
