@extends('layouts.dashboard')

@section('title', 'Profile Siswa')

@section('content')
<!-- Dashboard Button -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
    <a href="{{ route('siswa.dashboard') }}" 
       style="
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-radius: 0.5rem;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
       "
       onmouseover="this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.3)'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)';">
        <i class="fas fa-th-large"></i> Dashboard
    </a>
</div>

<div class="welcome-card">
    <h2><i class="fas fa-user-circle"></i> Profile Siswa</h2>
    <p>Berikut adalah data detail profil Anda sebagai siswa.</p>
</div>

<div class="content-section">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
            <!-- Header dengan warna gradien -->
            <div style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); padding: 2rem; text-align: center; color: white;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 style="margin: 0; font-size: 1.5rem;">{{ $siswa->user->nama_lengkap }}</h3>
            </div>

            <!-- Informasi Detail -->
            <div style="padding: 2rem;">
                <!-- Nama Lengkap -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-id-card" style="color: #0369a1; margin-right: 0.5rem;"></i> Nama Lengkap
                    </label>
                    <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                        {{ $siswa->user->nama_lengkap }}
                    </p>
                </div>

                <!-- NIS -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-barcode" style="color: #0369a1; margin-right: 0.5rem;"></i> Nomor Induk Siswa (NIS)
                    </label>
                    <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                        {{ $siswa->nis }}
                    </p>
                </div>

                <!-- Kelas -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-chalkboard-user" style="color: #0369a1; margin-right: 0.5rem;"></i> Kelas
                    </label>
                    <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                        @if($siswa->kelas)
                            <span style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-block;">
                                {{ $siswa->kelas->nama_kelas }} - {{ $siswa->kelas->jurusan }}
                            </span>
                        @else
                            <span style="color: #999; font-style: italic;">Belum ditentukan</span>
                        @endif
                    </p>
                </div>

                <!-- Jenis Kelamin -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-venus-mars" style="color: #0369a1; margin-right: 0.5rem;"></i> Jenis Kelamin
                    </label>
                    <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                        @if($siswa->jenis_kelamin === 'L')
                            <i class="fas fa-mars" style="color: #3b82f6; margin-right: 0.5rem;"></i> Laki-laki
                        @elseif($siswa->jenis_kelamin === 'P')
                            <i class="fas fa-venus" style="color: #ec4899; margin-right: 0.5rem;"></i> Perempuan
                        @else
                            <span style="color: #999; font-style: italic;">Belum ditentukan</span>
                        @endif
                    </p>
                </div>

                <!-- Agama -->
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-hands-praying" style="color: #0369a1; margin-right: 0.5rem;"></i> Agama
                    </label>
                    <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                        @if($siswa->agama)
                            {{ $siswa->agama }}
                        @else
                            <span style="color: #999; font-style: italic;">Belum ditentukan</span>
                        @endif
                    </p>
                </div>

                <!-- Semester -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; font-weight: 600;">
                        <i class="fas fa-calendar" style="color: #0369a1; margin-right: 0.5rem;"></i> Semester
                    </label>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <p style="margin: 0; font-size: 1.1rem; color: #2d3748; font-weight: 500;">
                            @if($siswa->semester)
                                <span style="background: #10b981; color: white; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-block;">
                                    {{ $siswa->semester }}
                                </span>
                            @else
                                <span style="color: #999; font-style: italic;">Belum ditentukan</span>
                            @endif
                        </p>
                        <button type="button" onclick="editSemester()" class="btn btn-edit" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; background: #0369a1; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem;">
                            <i class="fas fa-edit"></i> Ubah
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-edit:hover {
        background-color: #0284c7 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(3, 105, 161, 0.3);
    }
</style>

<script>
function editSemester() {
    const currentSemester = `{{ $siswa->semester ?? '' }}`;
    const siswaKelas = `{{ $siswa->kelas->nama_kelas ?? '' }}`;
    
    let options = '<option value="">-- Pilih Semester --</option>';
    
    // Tentukan opsi semester berdasarkan kelas
    // Check XII, XI terlebih dahulu sebelum X (karena XI.charAt(0) === 'X')
    // Tampilkan semua semester (Ganjil dan Genap) tapi Genap akan trigger warning
    if (siswaKelas.includes('XII')) {
        options += `
            <option value="XII Semester Ganjil 2025/2026" ${currentSemester === 'XII Semester Ganjil 2025/2026' ? 'selected' : ''}>XII Semester Ganjil 2025/2026</option>
            <option value="XII Semester Genap 2025/2026" ${currentSemester === 'XII Semester Genap 2025/2026' ? 'selected' : ''}>XII Semester Genap 2025/2026</option>
        `;
    } else if (siswaKelas.includes('XI')) {
        options += `
            <option value="XI Semester Ganjil 2025/2026" ${currentSemester === 'XI Semester Ganjil 2025/2026' ? 'selected' : ''}>XI Semester Ganjil 2025/2026</option>
            <option value="XI Semester Genap 2025/2026" ${currentSemester === 'XI Semester Genap 2025/2026' ? 'selected' : ''}>XI Semester Genap 2025/2026</option>
        `;
    } else if (siswaKelas.includes('X')) {
        options += `
            <option value="X Semester Ganjil 2025/2026" ${currentSemester === 'X Semester Ganjil 2025/2026' ? 'selected' : ''}>X Semester Ganjil 2025/2026</option>
            <option value="X Semester Genap 2025/2026" ${currentSemester === 'X Semester Genap 2025/2026' ? 'selected' : ''}>X Semester Genap 2025/2026</option>
        `;
    }
    
    Swal.fire({
        title: 'Ubah Semester',
        html: `
            <div style="text-align: left; margin: 1rem 0;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Pilih Semester Baru:</label>
                <select id="semesterSelect" style="width: 100%; padding: 0.6rem; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem;">
                    ${options}
                </select>
                <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                    <i class="fas fa-info-circle"></i> Anda dapat mengubah semester hanya dalam periode yang berlaku untuk tingkat kelas Anda.
                </p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Simpan Perubahan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#0369a1',
        preConfirm: () => {
            const semester = document.getElementById('semesterSelect').value;
            if (!semester) {
                Swal.showValidationMessage('Pilih semester terlebih dahulu');
                return false;
            }
            
            // Check apakah semester yang dipilih adalah Genap
            if (semester.includes('Genap')) {
                Swal.fire({
                    title: 'Belum Menjalani Tahun Ajaran',
                    text: 'Semester Genap 2025/2026 belum dimulai. Silakan pilih Semester Ganjil 2025/2026.',
                    icon: 'warning',
                    confirmButtonText: 'Kembali',
                    confirmButtonColor: '#f59e0b'
                });
                return false;
            }
            
            return semester;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateSemester(result.value);
        }
    });
}

function updateSemester(semester) {
    // Show loading
    Swal.fire({
        title: 'Menyimpan...',
        html: '<p>Mohon tunggu sebentar</p>',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('{{ route("siswa.profile.update-semester") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            semester: semester
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#0369a1'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error',
                confirmButtonColor: '#0369a1'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#0369a1'
        });
    });
}
</script>
@endsection
