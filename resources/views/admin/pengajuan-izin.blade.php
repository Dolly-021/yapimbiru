@extends('layouts.dashboard')

@section('title', 'Pengajuan Izin')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-file-medical"></i> Pengajuan Izin Siswa</h2>
    <p>Menyediakan dan mengatur fitur pengajuan izin digital (hadir, izin, sakit, alpha)</p>
</div>

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Pengajuan Izin</h3>
    </div>

    @if($izin->count() > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Siswa</th>
                    <th style="width: 12%;">Kelas</th>
                    <th style="width: 10%;">Tipe</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">Mata Pelajaran</th>
                    <th style="width: 18%;">Alasan/Keterangan</th>
                    <th style="width: 8%;">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @foreach($izin as $index => $item)
                <tr>
                    <td>{{ $izin->firstItem() + $index }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ $item->siswa->user->nama_lengkap }}</div>
                        <small style="color: #718096;">NIS: {{ $item->siswa->nis }}</small>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $item->siswa->kelas->nama_kelas }}</span>
                    </td>
                    <td>
                        @php
                            $tipeClass = [
                                'sakit' => 'badge-warning',
                                'izin' => 'badge-primary'
                            ];
                        @endphp
                        <span class="badge {{ $tipeClass[$item->tipe] ?? 'badge-secondary' }}">
                            {{ ucfirst($item->tipe) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>
                        @if($item->jadwal)
                            <small style="color: #3b82f6; font-weight: 500;">{{ $item->jadwal->mata_pelajaran }}</small>
                        @else
                            <small style="color: #cbd5e0;">-</small>
                        @endif
                    </td>
                    <td>
                        @if($item->tipe == 'sakit')
                            <small style="color: #cbd5e0;">-</small>
                        @elseif($item->alasan)
                            <small style="color: #4a5568;">{{ Str::limit($item->alasan, 50) }}</small>
                        @else
                            <small style="color: #cbd5e0;">-</small>
                        @endif
                    </td>
                    <td>
                        @if($item->bukti_file)
                        <a href="{{ route('izin.download-bukti', $item->id_izin) }}" class="btn btn-sm" style="background: #3b82f6; color: white; padding: 0.25rem 0.5rem; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.2s ease;"
                           onmouseover="this.style.background='#2563eb'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';"
                           onmouseout="this.style.background='#3b82f6'; this.style.boxShadow='none';">
                            <i class="fas fa-download"></i>
                        </a>
                        @else
                        <small style="color: #cbd5e0;">-</small>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $izin->links() }}
    </div>
    @else
    <div style="padding: 3rem; text-align: center; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-file-medical" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p style="margin: 0; font-size: 1.1rem;">Belum ada pengajuan izin</p>
    </div>
    @endif
</div>

<!-- Modal Reject -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin: 0 0 1rem 0; color: #2d3748;">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i> Tolak Pengajuan Izin
        </h3>
        <p style="color: #718096; margin-bottom: 1.5rem;">
            Anda akan menolak izin dari <strong id="rejectNamaSiswa"></strong>
        </p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="alasan">Alasan Penolakan</label>
                <textarea name="alasan" id="alasan" class="form-control" rows="3" required placeholder="Tuliskan alasan penolakan..."></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn" style="background: #ef4444; color: white; flex: 1;">
                    <i class="fas fa-times"></i> Tolak Izin
                </button>
                <button type="button" class="btn" style="background: #6b7280; color: white; flex: 1;" onclick="closeRejectModal()">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.table-container {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s;
}

.data-table tbody tr:hover {
    background: #f7fafc;
}

.data-table td {
    padding: 1rem;
    font-size: 0.875rem;
    color: #2d3748;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-info {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-primary {
    background: #dbeafe;
    color: #1e40af;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.badge-secondary {
    background: #e5e7eb;
    color: #374151;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2d3748;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e0;
    border-radius: 8px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
</style>

<script>
function showRejectModal(idIzin, namaSiswa) {
    document.getElementById('rejectModal').style.display = 'flex';
    document.getElementById('rejectNamaSiswa').textContent = namaSiswa;
    document.getElementById('rejectForm').action = '{{ url("admin/pengajuan-izin") }}/' + idIzin + '/reject';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('alasan').value = '';
}
</script>
@endsection
