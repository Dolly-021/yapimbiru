@extends('layouts.dashboard')

@section('title', 'Guru Dashboard')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-chalkboard-teacher"></i> Selamat Datang, {{ get_first_name() }}!</h2>
    <p>Halo <strong>{{ auth()->user()->nama_lengkap }}</strong>, selamat datang di dashboard Guru.</p>
    <p>Anda dapat mencatat kehadiran, mengelola materi pembelajaran, dan memantau aktivitas siswa.</p>
</div>

<div class="stats-grid">
    <div class="stat-card" style="cursor: pointer; transition: all 0.2s;" onclick="window.location.href='{{ route('guru.kelas') }}';" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
        <div class="stat-icon">
            <i class="fas fa-school"></i>
        </div>
        <div class="stat-value">{{ $totalKelas ?? '--' }}</div>
        <div class="stat-label">Kelas Diampu</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-value" style="font-size: 1rem;">
            @if($statusAbsen['sudah_masuk'])
                <span style="color: {{ strpos($statusAbsen['status_kehadiran'], 'Tepat Waktu') !== false ? '#10b981' : '#ef4444' }}; font-weight: 600;">
                    {{ strpos($statusAbsen['status_kehadiran'], 'Tepat Waktu') !== false ? '✓' : '⚠' }}
                </span>
            @else
                <span style="color: #6b7280;">--</span>
            @endif
        </div>
        <div class="stat-label" style="font-size: 0.85rem; font-weight: 500; color: #374151;">
            {{ $statusAbsen['status_kehadiran'] }}
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-folder"></i>
        </div>
        <div class="stat-value">{{ $totalMateri ?? '--' }}</div>
        <div class="stat-label">Materi Diunggah</div>
    </div>
</div>

<div class="content-section">
    <h3 class="section-title"><i class="fas fa-tasks"></i> Fitur Guru</h3>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <!-- 1. Absen Kehadiran -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-fingerprint"></i> Absen Kehadiran
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Mencatat jam masuk dan jam keluar setiap hari kerja
            </p>
            
            <!-- Status Absen -->
            <div data-absen-info>
                @if($statusAbsen['sudah_masuk'] && $statusAbsen['sudah_keluar'])
                    <span class="badge bg-success">✓ Masuk: {{ $statusAbsen['jam_masuk'] }} | Keluar: {{ $statusAbsen['jam_keluar'] }}</span>
                @elseif($statusAbsen['sudah_masuk'] && !$statusAbsen['sudah_keluar'])
                    <span class="badge bg-warning text-dark">Masuk: {{ $statusAbsen['jam_masuk'] }} | Silakan absen keluar</span>
                @else
                @endif
            </div>
            
            <div style="margin-top: 0.75rem;">
                @if($statusAbsen['sudah_masuk'] && $statusAbsen['sudah_keluar'])
                    <button id="btnAbsenMasuk" class="btn btn-primary btn-sm" disabled style="opacity: 0.6; margin-bottom: 10px">
                        <i class="fas fa-sign-in-alt"></i> ✓ Sudah Absen Masuk
                    </button>
                    <button id="btnAbsenKeluar" class="btn btn-secondary btn-sm" disabled style="opacity: 0.6">
                        <i class="fas fa-sign-out-alt"></i> ✓ Sudah Absen Keluar
                    </button>
                @elseif($statusAbsen['sudah_masuk'] && !$statusAbsen['sudah_keluar'])
                    <button id="btnAbsenMasuk" class="btn btn-primary btn-sm" disabled style="opacity: 0.6; margin-bottom: 10px">
                        <i class="fas fa-sign-in-alt"></i> ✓ Sudah Absen Masuk
                    </button>
                    <button id="btnAbsenKeluar" class="btn btn-warning btn-sm" onclick="absenKeluar()" style="margin-bottom: 10px">
                        <i class="fas fa-sign-out-alt"></i> Absen Keluar
                    </button>
                @else
                    <button id="btnAbsenMasuk" class="btn btn-primary btn-sm" onclick="absenMasuk()" style="margin-bottom: 10px">
                        <i class="fas fa-sign-in-alt"></i> Absen Masuk
                    </button>
                    <button id="btnAbsenKeluar" class="btn btn-secondary btn-sm" disabled style="opacity: 0.6">
                        <i class="fas fa-sign-out-alt"></i> Absen Keluar
                    </button>
                @endif
            </div>
        </div>

        <!-- 2. Absen Siswa -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clipboard-check"></i> Absen Siswa
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Mencatat kehadiran siswa sesuai kelas yang diampu
            </p>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                <a href="{{ route('guru.absen.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-check-square"></i> Buat Absen
                </a>
            </div>
        </div>

        <!-- 3. Lihat Pengajuan Izin -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt"></i> Lihat Pengajuan Izin
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Pantau pengajuan izin siswa untuk kelas yang Anda ampu
            </p>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                <a href="{{ route('guru.izin') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Pengajuan
                </a>
            </div>
        </div>

        <!-- 4. Kegiatan Sekolah -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-alt"></i> Kegiatan Sekolah
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Lihat jadwal kegiatan sekolah mendatang dan sedang berlangsung
            </p>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                <a href="{{ route('guru.kegiatan') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Kegiatan
                </a>
            </div>
        </div>

        <!-- 5. Kelola Tugas -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-tasks"></i> Kelola Tugas
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Membuat tugas, melihat pengumpulan, dan memberikan nilai
            </p>
            <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                <a href="{{ route('guru.tugas') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> Lihat Tugas
                </a>
            </div>
        </div>

        <!-- 6. Lihat Data Kehadiran -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-bar"></i> Data Kehadiran
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat daftar pertemuan dan data kehadiran per mata pelajaran
            </p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('guru.data-kehadiran') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Data
                </a>
            </div>
        </div>

        <!-- 6b. Laporan Per Bulan Siswa -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #ea580c;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-alt"></i> Laporan Per Bulan Siswa
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Rekapitulasi kehadiran siswa per bulan dengan filter kelas
            </p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('guru.laporan-per-bulan-siswa') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-file-alt"></i> Lihat Laporan
                </a>
            </div>
        </div>

        <!-- 6. Kelola Materi -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-edit"></i> Kelola Materi
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Memperbarui atau menghapus file materi pembelajaran
            </p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('guru.materi') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-cog"></i> Kelola
                </a>
            </div>
        </div>

        <!-- 7. Laporan Bulanan -->
        <div style="padding: 1.5rem; background: #f7fafc; border-radius: 10px; border-left: 4px solid #0369a1;">
            <h4 style="color: #2d3748; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt"></i> Laporan Bulanan
            </h4>
            <p style="color: #718096; font-size: 0.9rem; margin-bottom: 1rem;">
                Melihat rekap kehadiran per bulan untuk evaluasi
            </p>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('guru.laporan-bulanan') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Variabel global untuk menyimpan jam masuk
let jamMasukDicatat = null;

function absenMasuk() {
    // Ambil waktu dari browser
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const jamBrowser = `${hours}:${minutes}:${seconds}`;
    
    // Step 1: Tampilkan popup konfirmasi dengan jam dari BROWSER
    Swal.fire({
        title: 'Konfirmasi Absen Masuk',
        text: `Anda akan absen masuk pada jam ${jamBrowser}. Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Absen Masuk',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Simpan jam masuk ke variabel global
            jamMasukDicatat = jamBrowser;
            
            // Step 2: User sudah setuju, sekarang benar-benar absen
            Swal.fire({
                title: 'Mengirim...',
                text: 'Memproses absen masuk...',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ route("guru.absen-masuk") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                console.log('Absen Masuk Response:', data);
                
                if (data.success) {
                    // Update stat card Jam Masuk Hari Ini
                    const jamMasukCard = document.querySelector('.stat-card:nth-child(1) .stat-value');
                    if (jamMasukCard) {
                        jamMasukCard.textContent = jamMasukDicatat;
                    }
                    
                    // Update badge info dengan jam yang disimpan
                    const infoBadge = document.querySelector('[data-absen-info]');
                    if (infoBadge) {
                        infoBadge.innerHTML = `<span class="badge bg-warning text-dark">Masuk: ${jamMasukDicatat} | Silakan absen keluar</span>`;
                    }
                    
                    // Update tombol berdasarkan ID
                    const masukBtn = document.getElementById('btnAbsenMasuk');
                    const keluarBtn = document.getElementById('btnAbsenKeluar');
                    
                    if (masukBtn) {
                        masukBtn.disabled = true;
                        masukBtn.style.opacity = '0.6';
                        masukBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> ✓ Sudah Absen Masuk';
                    }
                    
                    if (keluarBtn) {
                        keluarBtn.disabled = false;
                        keluarBtn.style.opacity = '1';
                        keluarBtn.onclick = function() { absenKeluar(); };
                        keluarBtn.classList.remove('btn-secondary');
                        keluarBtn.classList.add('btn-warning');
                    }
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `✅ Absen masuk berhasil dicatat pada jam ${jamMasukDicatat}`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan: ' + error.message, 'error');
            });
        }
    });
}

function absenKeluar() {
    // Ambil waktu dari browser
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const jamBrowser = `${hours}:${minutes}:${seconds}`;
    
    // Step 1: Tampilkan popup konfirmasi dengan jam dari BROWSER
    Swal.fire({
        title: 'Konfirmasi Absen Keluar',
        text: `Anda akan absen keluar pada jam ${jamBrowser}. Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Absen Keluar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Step 2: User sudah setuju, sekarang benar-benar absen
            Swal.fire({
                title: 'Mengirim...',
                text: 'Memproses absen keluar...',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('{{ route("guru.absen-keluar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                console.log('Absen Keluar Response:', data);
                
                if (data.success) {
                    // Gunakan jam masuk yang disimpan di variabel global
                    const jamMasukFinnal = jamMasukDicatat || data.jam_masuk;
                    
                    // Update badge info dengan jam yang disimpan
                    const infoBadge = document.querySelector('[data-absen-info]');
                    if (infoBadge) {
                        infoBadge.innerHTML = `<span class="badge bg-success">✓ Masuk: ${jamMasukFinnal} | Keluar: ${jamBrowser}</span>`;
                    }
                    
                    // Update tombol berdasarkan ID
                    const keluarBtn = document.getElementById('btnAbsenKeluar');
                    const masukBtn = document.getElementById('btnAbsenMasuk');
                    
                    if (keluarBtn) {
                        keluarBtn.disabled = true;
                        keluarBtn.style.opacity = '0.6';
                        keluarBtn.innerHTML = '<i class="fas fa-sign-out-alt"></i> ✓ Sudah Absen Keluar';
                        keluarBtn.classList.remove('btn-warning');
                        keluarBtn.classList.add('btn-secondary');
                        keluarBtn.onclick = null;
                    }
                    
                    if (masukBtn) {
                        masukBtn.disabled = true;
                    }
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `✅ Absen keluar berhasil dicatat pada jam ${jamBrowser}`,
                        icon: 'success'
                    });
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan: ' + error.message, 'error');
            });
        }
    });
}
</script>
@endsection