@extends('layouts.dashboard')

@section('title', 'Verifikasi Pendaftaran Guru')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;">
            <i class="fas fa-user-check"></i> Verifikasi Pendaftaran Guru
        </h3>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
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

    @if($pendingGuru->count() > 0)
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">No</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Nama Lengkap</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Email</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">No HP</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Tanggal Daftar</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600; width: 120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingGuru as $index => $user)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 1rem;">{{ $index + 1 }}</td>
                    <td style="padding: 1rem; font-weight: 500;">{{ $user->nama_lengkap }}</td>
                    <td style="padding: 1rem;">{{ $user->email }}</td>
                    <td style="padding: 1rem;">{{ $user->no_hp ?? '-' }}</td>
                    <td style="padding: 1rem;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td style="padding: 1rem; text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center;">
                            <button 
                                onclick="approveGuru({{ $user->id_user }}, '{{ $user->nama_lengkap }}')" 
                                class="btn btn-success btn-sm" 
                                style="min-width: 45px; padding: 0.5rem;"
                                title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                            <button 
                                onclick="rejectGuru({{ $user->id_user }}, '{{ $user->nama_lengkap }}')" 
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
        <p style="color: #718096; font-size: 1.1rem;">Tidak ada pendaftaran guru yang menunggu verifikasi</p>
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
</style>

<script>
function approveGuru(userId, namaGuru) {
    Swal.fire({
        title: 'Approve Pendaftaran?',
        html: `Anda akan menyetujui pendaftaran guru:<br><strong>${namaGuru}</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Ya, Approve',
        cancelButtonText: '<i class="fas fa-times"></i> Batal',
        confirmButtonColor: '#48bb78',
        cancelButtonColor: '#718096'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form approve
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/verifikasi-guru/${userId}/approve`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function rejectGuru(userId, namaGuru) {
    Swal.fire({
        title: 'Reject Pendaftaran?',
        html: `Anda akan menolak pendaftaran guru:<br><strong>${namaGuru}</strong><br><br>Status akun akan diubah menjadi 'Ditolak'.`,
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
            // Submit form reject
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/verifikasi-guru/${userId}/reject`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const alasan = document.createElement('input');
            alasan.type = 'hidden';
            alasan.name = 'alasan';
            alasan.value = result.value || '';
            
            form.appendChild(csrfToken);
            form.appendChild(alasan);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
