@extends('layouts.dashboard')

@section('content')
<style>

    .izin-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .section-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .section-desc {
        color: #718096;
        margin-bottom: 1.5rem;
    }

    /* TABLE STYLE */
    .izin-table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .izin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .izin-table thead {
        background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
        color: white;
    }

    .izin-table thead th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .izin-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: background 0.2s ease;
    }

    .izin-table tbody tr:hover {
        background: #f8fafc;
    }

    .izin-table tbody tr:last-child {
        border-bottom: none;
    }

    .izin-table tbody td {
        padding: 1.2rem 1rem;
        color: #1e293b;
        vertical-align: middle;
    }

    /* Cell No */
    .cell-no {
        text-align: center;
        font-weight: 600;
        width: 60px;
    }

    /* Cell User */
    .cell-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .avatar-box {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #dbeafe;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.3rem;
        color: #1e40af;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .user-role {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Cell Jenis Izin */
    .badge-izin {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-sakit {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-umum {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Cell Alasan */
    .cell-alasan {
        max-width: 300px;
        color: #475569;
        font-size: 0.9rem;
    }

    /* Cell Tanggal */
    .cell-tanggal {
        color: #64748b;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    /* Cell Aksi */
    .cell-aksi {
        text-align: center;
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 1rem;
        background: #0284c7;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-detail:hover {
        background: #0369a1;
    }

    .btn-lihat-bukti {
        color: #0284c7;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.85rem;
    }

    .btn-lihat-bukti:hover {
        text-decoration: underline;
    }

    .empty-state {
        text-align: center;
        background: #dbeafe;
        border-left: 4px solid #1e40af;
        padding: 2rem;
        font-weight: 600;
        border-radius: 8px;
        color: #1e3a8a;
    }

    @media (max-width: 768px) {
        .izin-table {
            font-size: 0.85rem;
        }
        
        .izin-table thead th,
        .izin-table tbody td {
            padding: 0.8rem 0.6rem;
        }
    }

</style>

<div class="izin-wrapper">

    <h2 class="section-title">Lihat Pengajuan Izin</h2>
    <p class="section-desc">Pantau semua pengajuan izin siswa dan guru</p>

    @if($izinList->count() > 0)
    <div class="izin-table-wrapper">
        <table class="izin-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Diajukan Ke</th>
                    <th>Jenis Izin</th>
                    <th>Alasan / Keterangan</th>
                    <th>Tanggal</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($izinList as $index => $izin)
                    <tr>
                        <!-- No -->
                        <td class="cell-no">{{ $index + 1 }}</td>

                        <!-- Nama & Role -->
                        <td>
                            <div class="cell-user">
                                <div class="avatar-box">
                                    {{ strtoupper(substr($izin->user->nama_lengkap ?? $izin->user->name, 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <span class="user-name">{{ $izin->user->nama_lengkap ?? $izin->user->name }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Kelas -->
                        <td>
                            <span class="user-role">
                                {{ $izin->user->siswa && $izin->user->siswa->kelas ? ($izin->user->siswa->kelas->nama_kelas ?? '-') : '-' }}
                            </span>
                        </td>

                        <!-- Diajukan Ke (Guru) -->
                        <td>
                            <span class="user-role">
                                {{ $izin->guru && $izin->guru->user ? $izin->guru->user->nama : '-' }}
                            </span>
                        </td>

                        <!-- Jenis Izin -->
                        <td>
                            @if($izin->tipe === 'sakit')
                                <span class="badge-izin badge-sakit">Sakit</span>
                            @else
                                <span class="badge-izin badge-umum">Izin</span>
                            @endif
                        </td>

                        <!-- Alasan / Keterangan -->
                        <td class="cell-alasan">
                            @if($izin->tipe === 'sakit')
                                -
                            @else
                                {{ $izin->alasan }}
                            @endif
                        </td>

                        <!-- Tanggal -->
                        <td class="cell-tanggal">
                            {{ $izin->tanggal ? \Carbon\Carbon::parse($izin->tanggal)->format('d M Y, H:i') : '-' }}
                        </td>

                        <!-- Bukti File -->
                        <td class="cell-aksi">
                            @if($izin->bukti_file)
                                <a class="btn-lihat-bukti" href="{{ asset('storage/' . $izin->bukti_file) }}" target="_blank">Lihat Bukti</a>
                            @else
                                <span style="color: #94a3b8; font-size: 0.85rem;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">Tidak ada permohonan izin</div>
    @endif

    @if($izinList->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $izinList->links() }}
    </div>
    @endif

</div>

@endsection