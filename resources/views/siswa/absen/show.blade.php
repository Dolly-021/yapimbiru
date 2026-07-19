@extends('layouts.dashboard')

@section('title', 'Presensi - ' . ($mapelName ?? 'N/A'))

@section('content')
<div class="container-fluid">
    <!-- Navigation Buttons -->
    <div style="display: flex; gap: 0.75rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="{{ route('siswa.dashboard') }}" 
           style="padding: 0.75rem 1.5rem; background-color: #3b82f6; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;"
           onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
           onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
            <i class="fas fa-home"></i> Beranda
        </a>
        <a href="{{ route('siswa.absen.index') }}" 
           style="padding: 0.75rem 1.5rem; background-color: #6b7280; color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.5rem;"
           onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
           onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
            <i class="fas fa-list"></i> Presensi Kelas
        </a>
        <button style="padding: 0.75rem 1.5rem; background-color: #10b981; color: white; border-radius: 0.5rem; border: none; font-weight: 500; font-size: 0.9375rem; cursor: default; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-book"></i> {{ $mapelName ?? 'N/A' }}
        </button>
    </div>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="mb-1">
                <i class="fas fa-clipboard-list"></i> Presensi - 
                <strong>{{ $mapelName ?? 'N/A' }}</strong>, 
                <strong>Kelas {{ $jadwal?->kelas?->nama_kelas ?? 'N/A' }}</strong>
            </h3>
            <p class="text-muted mb-0">
                <i class="fas fa-user-tie"></i> Guru: <strong>{{ $guruName ?? 'N/A' }}</strong>
            </p>
        </div>
    </div>

    <!-- Main Content -->
    @if($absens->isEmpty())
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Belum ada pertemuan.</strong> Guru belum membuat absen untuk mata pelajaran ini.
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($absens as $index => $absen)
                @php
                    $pertemuanNumber = $index + 1;
                    $now = now();
                    $isOpen = $now >= $absen->jam_buka && $now <= $absen->jam_tutup;
                    // Gunakan collection yang sudah di-load, jangan query baru
                    $absenSiswa = $absen->absenSiswas->firstWhere('id_siswa', \Illuminate\Support\Facades\Auth::user()->siswa->id_siswa);

                    $statusColors = [
                        'hadir' => '#10b981',
                        'tidak_hadir' => '#6b7280',
                        'izin' => '#f59e0b',
                        'sakit' => '#3b82f6'
                    ];
                    
                    // Button aktif HANYA jika belum pernah di-submit (waktu_absen NULL)
                    $canPresent = $isOpen && $absenSiswa && $absenSiswa->waktu_absen === null;
                    
                    // Status display
                    $displayStatus = $absenSiswa?->status ?? 'belum_absen';
                    $isClosed = !$isOpen;
                    $isLocked = !$canPresent && $isOpen;
                @endphp
                <div style="
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.75rem;
                    padding: 1.25rem;
                    display: flex;
                    gap: 1rem;
                    align-items: flex-start;
                    transition: all 0.3s ease;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                "
                onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'; this.style.transform='translateY(0)';">
                    
                    <!-- No Badge -->
                    <div style="
                        background: #dbeafe;
                        color: #1e40af;
                        width: 3rem;
                        height: 3rem;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 0.5rem;
                        font-weight: 700;
                        font-size: 1.125rem;
                        flex-shrink: 0;
                    ">
                        {{ $pertemuanNumber }}
                    </div>

                    <!-- Content -->
                    <div style="flex: 1;">
                        <!-- Judul Pertemuan -->
                        <h5 style="margin: 0 0 0.5rem 0; color: #1f2937; font-weight: 600;">
                            Pertemuan {{ $pertemuanNumber }}
                            @if($absenSiswa && $absenSiswa->waktu_absen === null)
                                <span style="
                                    display: inline-block;
                                    background: #fecaca;
                                    color: #991b1b;
                                    padding: 0.25rem 0.75rem;
                                    border-radius: 0.25rem;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    margin-left: 0.5rem;
                                ">Belum Absen</span>
                            @elseif($absenSiswa)
                                @php
                                    $statusBadgeMap = [
                                        'hadir' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Hadir'],
                                        'izin' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Izin'],
                                        'sakit' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Sakit'],
                                        'tidak_hadir' => ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => 'Absen'],
                                    ];
                                    $badgeInfo = $statusBadgeMap[$absenSiswa->status] ?? ['bg' => '#f3f4f6', 'text' => '#374151', 'label' => ucfirst($absenSiswa->status)];
                                @endphp
                                <span style="
                                    display: inline-block;
                                    background: {{ $badgeInfo['bg'] }};
                                    color: {{ $badgeInfo['text'] }};
                                    padding: 0.25rem 0.75rem;
                                    border-radius: 0.25rem;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    margin-left: 0.5rem;
                                ">{{ $badgeInfo['label'] }}</span>
                            @endif
                        </h5>

                        <!-- Tanggal & Hari -->
                        <p style="margin: 0.5rem 0; color: #6b7280; font-size: 0.9375rem;">
                            <i class="fas fa-calendar-alt"></i>
                            {{ \Carbon\Carbon::parse($absen->jam_buka)->locale('id_ID')->translatedFormat('l, d F Y') }}
                        </p>

                        <!-- Topik Materi -->
                        <p style="margin: 0.25rem 0 0.5rem 0; color: #9ca3af; font-size: 0.875rem;">
                            {{ $absen->keterangan ?? '(tanpa topik)' }}
                        </p>

                        <!-- Jam Buka Tutup -->
                        <p style="margin: 0; color: #374151; font-size: 0.875rem;">
                            <i class="fas fa-clock"></i>
                            <strong>Buka:</strong> {{ $absen->jam_buka->format('H:i') }} &nbsp;
                            <strong>Tutup:</strong> {{ $absen->jam_tutup->format('H:i') }}
                        </p>
                    </div>

                    <!-- Status & Action (Right Side) -->
                    <div style="
                        display: flex;
                        flex-direction: column;
                        align-items: flex-end;
                        gap: 0.75rem;
                    ">
                        <!-- Status Display -->
                        <div>
                            @if($isClosed)
                                <span style="
                                    color: #d1d5db;
                                    font-size: 0.875rem;
                                    font-weight: 500;
                                ">Sudah Ditutup</span>
                            @elseif($absenSiswa && $absenSiswa->waktu_absen)
                                <span style="
                                    color: #0ea5e9;
                                    font-size: 0.875rem;
                                    font-weight: 500;
                                ">Dicatat pada {{ $absenSiswa->waktu_absen->format('H:i') }}</span>
                            @else
                                <span style="
                                    color: #ef4444;
                                    font-size: 0.875rem;
                                    font-weight: 500;
                                ">Belum Dicatat</span>
                            @endif
                        </div>

                        <!-- Action Button -->
                        <div>
                            @if($canPresent)
                                <!-- Tombol AKTIF - Belum pernah submit -->
                                <button type="button"
                                        class="btn btn-sm"
                                        style="
                                            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                                            color: white;
                                            border: none;
                                            padding: 0.5rem 1rem;
                                            border-radius: 0.375rem;
                                            font-weight: 500;
                                            cursor: pointer;
                                            transition: all 0.2s ease;
                                        "
                                        onmouseover="this.style.boxShadow='0 4px 6px rgba(37, 99, 235, 0.3)'; this.style.transform='scale(1.02)';"
                                        onmouseout="this.style.boxShadow='none'; this.style.transform='scale(1)';"
                                        onclick="markPresent({{ $absen->id }}, this)">
                                    <i class="fas fa-check-circle"></i> Isi Absen
                                </button>
                            @elseif($isClosed)
                                <!-- Presensi sudah tutup -->
                                <button class="btn btn-sm" disabled 
                                        style="
                                            background: #e5e7eb;
                                            color: #9ca3af;
                                            border: none;
                                            padding: 0.5rem 1rem;
                                            border-radius: 0.375rem;
                                            font-weight: 500;
                                            cursor: not-allowed;
                                        ">
                                    <i class="fas fa-lock"></i> Ditutup
                                </button>
                            @else
                                <!-- Sudah pernah submit - TERKUNCI -->
                                <button class="btn btn-sm" disabled 
                                        style="
                                            background: #e5e7eb;
                                            color: #9ca3af;
                                            border: none;
                                            padding: 0.5rem 1rem;
                                            border-radius: 0.375rem;
                                            font-weight: 500;
                                            cursor: not-allowed;
                                        ">
                                    <i class="fas fa-lock"></i> Terkunci
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item.active {
        color: #6b7280;
    }

    .table {
        margin-bottom: 0;
    }

    .table-light {
        background-color: #f3f4f6;
        font-weight: 600;
        color: #374151;
    }

    .table-hover tbody tr:hover {
        background-color: #f9fafb;
        transition: background-color 0.15s ease-in-out;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
        font-weight: 600;
    }

    .btn-sm {
        font-size: 0.8rem;
        transition: all 0.2s ease;
    }

    .btn-sm:not(:disabled):hover {
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .align-middle {
        vertical-align: middle;
    }

    .table-responsive {
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 0.85rem;
        }

        th, td {
            padding: 0.6rem 0.4rem !important;
        }

        .btn-sm {
            padding: 0.25rem 0.4rem !important;
            font-size: 0.7rem;
        }
    }
</style>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const storeUrl = '{{ route("siswa.absen.store") }}';
    
    console.log('Store URL:', storeUrl);
    console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
    
    function markPresent(absenId, button) {
        // Disable button sementara dan show loading
        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

        console.log('Sending request to:', storeUrl);
        console.log('Absen ID:', absenId);

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                absen_id: absenId,
                status: 'hadir'
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response type:', response.type);
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response body:', text);
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Disable button permanen dan ubah style
                button.disabled = true;
                button.style.opacity = '0.6';
                button.innerHTML = '<i class="fas fa-lock"></i> Terkunci';
                button.className = 'btn btn-sm btn-outline-secondary';

                // Show success message
                showNotification('Absen berhasil dicatat sebagai Hadir', 'success');

                // Update status badge
                const row = button.closest('tr');
                const statusBadge = row.querySelector('.badge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'Hadir';
                }
            } else {
                throw new Error(data.message || 'Gagal mencatat absen');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            showNotification('Error: ' + error.message, 'danger');
        });
    }

    function showNotification(message, type = 'success') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.setAttribute('role', 'alert');
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 4 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 4000);
    }
</script>

@endsection
