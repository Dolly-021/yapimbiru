@extends('layouts.dashboard')

@section('title', 'Detail Presensi Guru')

@section('content')
<style>
    .laporan-wrapper { max-width: 1200px; margin: 0 auto; padding: 1rem; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .section-title { font-size: 1.6rem; font-weight: 700; color: #2d3748; margin: 0; }
    .section-desc { color: #718096; margin-top: 0.25rem; }
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; }
    .btn-secondary { background: #e2e8f0; color: #2d3748; }
    .table-wrapper { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .table-header { padding: 1rem 1.5rem; background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%); color: white; }
    .table-header h3 { margin: 0; font-size: 1.1rem; }
    .presensi-table { width: 100%; border-collapse: collapse; }
    .presensi-table thead { background: #f8fafc; }
    .presensi-table th { padding: 0.75rem 1rem; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; font-size: 0.85rem; }
    .presensi-table td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #334155; }
    .presensi-table tbody tr:hover { background: #f8fafc; }
    .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .badge-hadir { background: #d1fae5; color: #065f46; }
    .badge-izin { background: #fef3c7; color: #92400e; }
    .badge-sakit { background: #fee2e2; color: #991b1b; }
    .badge-alpha { background: #f3f4f6; color: #374151; }
    .badge-terlambat { background: #fae8ff; color: #7c3aed; font-size: 0.7rem; margin-left: 0.5rem; }
    .empty-state { text-align: center; padding: 3rem; color: #64748b; }
</style>

<div class="laporan-wrapper">
    <!-- Header -->
    <div class="section-header">
        <div>
            <h2 class="section-title"><i class="fas fa-file-alt"></i> Detail Presensi: {{ $user->nama_lengkap ?? $user->name }}</h2>
            <p class="section-desc">Rekap kehadiran bulan {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->format('F Y') }}</p>
        </div>
        
        <div>
            <a href="{{ route('kepala_sekolah.rekap-presensi', ['bulan' => $bulan, 'tahun' => $tahun, 'tipe' => 'guru']) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Rekap
            </a>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="table-wrapper">
        <div class="table-header">
            <h3><i class="fas fa-list"></i> Detail Kehadiran Harian</h3>
        </div>
        
        @if($presensi->count() > 0)
        <table class="presensi-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Lokasi & Foto</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($presensi as $index => $p)
                @php
                    $tanggal = \Carbon\Carbon::parse($p->tanggal);
                    $jamKerjaStandar = '07:30:00';
                    $isTerlambat = $p->jam_masuk && $p->jam_masuk > $jamKerjaStandar && $p->status == 'hadir';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tanggal->format('d M Y') }}</td>
                    <td>{{ $tanggal->locale('id')->dayName }}</td>
                    <td>
                        {{ $p->jam_masuk ?? '-' }}
                        @if($isTerlambat)
                            <span class="badge badge-terlambat">Terlambat</span>
                        @endif
                    </td>
                    <td>{{ $p->jam_keluar ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($p->status) }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td>
                        @if($p->latitude && $p->longitude)
                            @if($p->alamat)
                                {{-- Data baru: alamat sudah tersimpan di DB --}}
                                <div style="font-size: 0.8rem; color: #374151; margin-bottom: 0.25rem; line-height: 1.4;">
                                    <i class="fas fa-map-marker-alt" style="color: #dc2626;"></i>
                                    {{ Str::limit($p->alamat, 100) }}
                                    <a href="https://maps.google.com/?q={{ $p->latitude }},{{ $p->longitude }}" target="_blank" style="color: #2563eb; font-size: 0.75rem; margin-left: 4px;" title="Buka di Google Maps">↗</a>
                                </div>
                            @else
                                {{-- Data lama: akan diisi oleh JS reverse geocoding --}}
                                <div class="lokasi-cell" data-lat="{{ $p->latitude }}" data-lng="{{ $p->longitude }}"
                                     style="font-size: 0.8rem; color: #374151; margin-bottom: 0.25rem; line-height: 1.4;">
                                    <i class="fas fa-map-marker-alt" style="color: #dc2626;"></i>
                                    <span class="alamat-text" style="font-style: italic; color: #94a3b8;">Memuat alamat...</span>
                                    <a href="https://maps.google.com/?q={{ $p->latitude }},{{ $p->longitude }}" target="_blank" style="color: #2563eb; font-size: 0.75rem; margin-left: 4px;" title="Buka di Google Maps">↗</a>
                                </div>
                            @endif
                        @else
                            <span style="font-size: 0.8rem; color: #94a3b8; display: block; margin-bottom: 0.25rem;"><i class="fas fa-times-circle"></i> Tidak ada GPS</span>
                        @endif

                        @if($p->foto_absen_masuk)
                            <a href="{{ asset('storage/foto_absen/' . $p->foto_absen_masuk) }}" target="_blank"
                               style="font-size: 0.8rem; text-decoration: none; color: #2563eb; display: block;">
                                <i class="fas fa-image"></i> Lihat Foto
                            </a>
                        @else
                            <span style="font-size: 0.8rem; color: #94a3b8; display: block;">Tidak ada foto</span>
                        @endif
                    </td>
                    <td>{{ $p->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>Tidak ada data presensi untuk bulan ini</p>
        </div>
        @endif
    </div>
</div>

<script>
// Reverse geocoding untuk data lama yang belum ada kolom alamat
// Jalankan satu per satu (sequential) agar tidak membanjiri Nominatim API
document.addEventListener('DOMContentLoaded', async function () {
    const cells = document.querySelectorAll('.lokasi-cell');
    for (const cell of cells) {
        const lat = cell.dataset.lat;
        const lng = cell.dataset.lng;
        const span = cell.querySelector('.alamat-text');
        if (!lat || !lng || !span) continue;
        try {
            // Jeda 1 detik antar request agar tidak diblokir Nominatim (rate limit 1 req/s)
            await new Promise(r => setTimeout(r, 1000));
            const res = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=id`,
                { headers: { 'Accept-Language': 'id' } }
            );
            const data = await res.json();
            if (data && data.display_name) {
                // Potong agar tidak terlalu panjang
                const alamat = data.display_name.length > 100
                    ? data.display_name.substring(0, 100) + '...'
                    : data.display_name;
                span.textContent = alamat;
                span.style.fontStyle = 'normal';
                span.style.color = '#374151';
            } else {
                span.textContent = `${parseFloat(lat).toFixed(5)}, ${parseFloat(lng).toFixed(5)}`;
                span.style.color = '#6b7280';
                span.style.fontStyle = 'normal';
            }
        } catch (e) {
            span.textContent = `${parseFloat(lat).toFixed(5)}, ${parseFloat(lng).toFixed(5)}`;
            span.style.color = '#6b7280';
            span.style.fontStyle = 'normal';
        }
    }
});
</script>
@endsection
