@extends('layouts.dashboard')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Daftar Tugas</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Lihat tugas yang diberikan oleh guru dan kumpulkan jawaban</p>
        </div>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                <strong>{{ session('success') }}</strong>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                <strong>{{ session('error') }}</strong>
            </div>
        </div>
    @endif

    @if($tugasWithStatus->isEmpty())
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>Belum ada tugas yang diberikan</p>
        </div>
    @else
        <div style="display: grid; gap: 1.5rem;">
            @foreach($tugasWithStatus as $t)
                @php
                    // Parse deadline - asumsikan sudah dalam timezone Asia/Jakarta (bukan UTC)
                    $deadline = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $t->deadline, 'Asia/Jakarta');
                    // Ambil waktu sekarang dalam timezone app
                    $now = \Carbon\Carbon::now('Asia/Jakarta');
                    
                    // Tentukan status: jika sekarang LEBIH BESAR dari deadline, maka terlambat
                    $isOverdue = $now->greaterThan($deadline);
                    
                    if ($isOverdue) {
                        // Sudah melewati deadline - hitung keterlambatan
                        $diff = $now->diff($deadline);
                        $timeDisplay = 'Terlambat ' . $diff->days . ' hari ' . $diff->h . ' jam ' . $diff->i . ' menit';
                    } else {
                        // Belum melewati deadline - hitung sisa waktu
                        $diff = $deadline->diff($now);
                        $timeDisplay = $diff->days . ' hari ' . $diff->h . ' jam ' . $diff->i . ' menit lagi';
                    }
                @endphp
                
                <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; border-left: 4px solid {{ $t->sudah_mengumpulkan ? '#10b981' : ($isOverdue ? '#ef4444' : '#0369a1') }};">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
                                {{ $t->judul_tugas }}
                            </h3>
                            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; color: #64748b; font-size: 0.9rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-user-tie"></i>
                                    <span>{{ $t->guru->user->nama_lengkap ?? 'Guru' }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $deadline->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                            @if($t->sudah_mengumpulkan)
                                <span style="background: #d1fae5; color: #065f46; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; white-space: nowrap;">
                                    <i class="fas fa-check-circle"></i> Sudah Dikumpulkan
                                </span>
                                @if($t->pengumpulan_data && $t->pengumpulan_data->nilai)
                                    <span style="background: #fef3c7; color: #92400e; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                        Nilai: {{ $t->pengumpulan_data->nilai }}
                                    </span>
                                @endif
                            @elseif($isOverdue)
                                <span style="background: #fee2e2; color: #991b1b; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; white-space: nowrap;">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $timeDisplay }}
                                </span>
                            @else
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; white-space: nowrap;">
                                    <i class="fas fa-clock"></i> {{ $timeDisplay }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <p style="color: #475569; line-height: 1.6; margin-bottom: 1.5rem; white-space: pre-wrap;">{{ Str::limit($t->deskripsi, 200) }}</p>

                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <a href="{{ route('siswa.tugas.detail', $t->id_tugas) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                        
                        @if($t->sudah_mengumpulkan)
                            <span style="color: #10b981; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem;">
                                <i class="fas fa-info-circle"></i>
                                Dikumpulkan: {{ \Carbon\Carbon::parse($t->pengumpulan_data->waktu_submit)->format('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
