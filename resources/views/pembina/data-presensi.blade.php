@extends('layouts.dashboard')

@section('title', 'Data Presensi')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2><i class="fas fa-clipboard-list"></i> Data Presensi</h2>
        <p>Lihat data presensi siswa dan guru (Read-Only)</p>
    </div>
    <x-dashboard-button />
</div>

<div class="content-section">
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        
        <!-- Filter Form -->
        <div style="margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0;">
            <form method="GET" action="{{ route('pembina.presensi') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                
                <!-- Tipe Presensi -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500; font-size: 0.9rem;">
                        <i class="fas fa-filter"></i> Tipe Presensi
                    </label>
                    <select name="tipe" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 0.95rem;">
                        <option value="guru" {{ $tipe === 'guru' ? 'selected' : '' }}>Data Guru</option>
                        <option value="siswa" {{ $tipe === 'siswa' ? 'selected' : '' }}>Data Siswa</option>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500; font-size: 0.9rem;">
                        <i class="fas fa-calendar"></i> Dari Tanggal
                    </label>
                    <input type="date" name="start_date" value="{{ $startDate }}" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 0.95rem;">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; color: #4a5568; font-weight: 500; font-size: 0.9rem;">
                        <i class="fas fa-calendar"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="end_date" value="{{ $endDate }}" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 0.95rem;">
                </div>

                <!-- Button -->
                <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                    <button type="submit" style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; flex: 1;">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('pembina.presensi') }}" style="background: #e2e8f0; color: #4a5568; padding: 0.75rem 1.5rem; border-radius: 6px; text-decoration: none; font-weight: 500; flex: 1; text-align: center;">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: bold;">{{ $presensi->total() }}</div>
                <div style="font-size: 0.85rem; opacity: 0.9;">Total Record</div>
            </div>
            <div style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: bold;">{{ $presensi->count() }}</div>
                <div style="font-size: 0.85rem; opacity: 0.9;">Halaman Ini</div>
            </div>
            <div style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); color: white; padding: 1rem; border-radius: 8px; text-align: center;">
                <div style="font-size: 0.85rem;">{{ ucfirst($tipe) }}</div>
                <div style="font-size: 1.5rem; font-weight: bold; margin-top: 0.25rem;">{{ $tipe === 'guru' ? 'Guru' : 'Siswa' }}</div>
            </div>
        </div>

        <!-- Data Table -->
        @if($presensi->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="background: #f7fafc; border-bottom: 2px solid #cbd5e0;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">No</th>
                            @if($tipe === 'guru')
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Nama Guru</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Tanggal</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Jam Masuk</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Jam Pulang</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Status</th>
                            @else
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">NIS</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Nama Siswa</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Kelas</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Tanggal</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 600; color: #2d3748;">Status</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($presensi as $index => $item)
                            <tr style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;">
                                <td style="padding: 1rem; color: #718096;">{{ ($presensi->currentPage() - 1) * 50 + $loop->iteration }}</td>
                                
                                @if($tipe === 'guru')
                                    <td style="padding: 1rem; color: #2d3748;">
                                        <strong>{{ $item->user->nama_lengkap ?? $item->user->name ?? '-' }}</strong>
                                    </td>
                                    <td style="padding: 1rem; color: #718096;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td style="padding: 1rem; color: #718096;">{{ $item->jam_masuk ?? '-' }}</td>
                                    <td style="padding: 1rem; color: #718096;">{{ $item->jam_pulang ?? '-' }}</td>
                                    <td style="padding: 1rem;">
                                        @if($item->jam_masuk)
                                            <span style="background: #d1fae5; color: #065f46; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                                                <i class="fas fa-check-circle"></i> Hadir
                                            </span>
                                        @else
                                            <span style="background: #fee2e2; color: #991b1b; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                                                <i class="fas fa-times-circle"></i> Tidak Hadir
                                            </span>
                                        @endif
                                    </td>
                                @else
                                    <td style="padding: 1rem; color: #2d3748;">
                                        <strong>{{ $item->siswa->user->nisn ?? '-' }}</strong>
                                    </td>
                                    <td style="padding: 1rem; color: #2d3748;">
                                        <strong>{{ $item->siswa->user->nama_lengkap ?? $item->siswa->user->name ?? '-' }}</strong>
                                    </td>
                                    <td style="padding: 1rem; color: #718096;">
                                        {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                                    </td>
                                    <td style="padding: 1rem; color: #718096;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td style="padding: 1rem;">
                                        @php
                                            $statusColor = [
                                                'hadir' => '#d1fae5',
                                                'hadir_text' => '#065f46',
                                                'sakit' => '#fef3c7',
                                                'sakit_text' => '#92400e',
                                                'izin' => '#dbeafe',
                                                'izin_text' => '#1e40af',
                                                'alfa' => '#fee2e2',
                                                'alfa_text' => '#991b1b',
                                            ];
                                            $status = strtolower($item->status ?? 'alfa');
                                        @endphp
                                        <span style="background: {{ $statusColor[$status] ?? $statusColor['alfa'] }}; color: {{ $statusColor[$status . '_text'] ?? $statusColor['alfa_text'] }}; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; text-transform: capitalize;">
                                            @if($status === 'hadir')
                                                <i class="fas fa-check-circle"></i>
                                            @elseif($status === 'sakit')
                                                <i class="fas fa-band-aid"></i>
                                            @elseif($status === 'izin')
                                                <i class="fas fa-hand-paper"></i>
                                            @else
                                                <i class="fas fa-times-circle"></i>
                                            @endif
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $presensi->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: #718096;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">Tidak ada data presensi</p>
                <p style="font-size: 0.9rem; color: #a0aec0;">Coba ubah filter tanggal atau tipe presensi</p>
            </div>
        @endif

    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h2 {
        color: #2d3748;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header p {
        color: #718096;
        font-size: 1rem;
    }

    .content-section {
        margin-bottom: 2rem;
    }

    /* Pagination styling */
    ::v-deep .pagination {
        display: flex;
        gap: 0.5rem;
        list-style: none;
        justify-content: center;
    }

    ::v-deep .pagination li {
        display: inline-block;
    }

    ::v-deep .pagination a,
    ::v-deep .pagination span {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e0;
        border-radius: 4px;
        text-decoration: none;
        color: #0369a1;
        transition: all 0.2s;
    }

    ::v-deep .pagination a:hover {
        background: #0369a1;
        color: white;
    }

    ::v-deep .pagination .active span {
        background: #0369a1;
        color: white;
        border-color: #0369a1;
    }

    ::v-deep .pagination .disabled span {
        color: #cbd5e0;
        cursor: not-allowed;
    }

    table tr:hover {
        background: #f7fafc;
    }
</style>
@endsection
