<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran - {{ $namaBulan }} {{ $tahun }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            background: white;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0369a1;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #0369a1;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #475569;
            font-weight: normal;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
        }
        
        .info-left, .info-right {
            width: 48%;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 120px;
            font-weight: 600;
            color: #475569;
        }
        
        .info-value {
            color: #1e293b;
        }
        
        .stats-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 25px;
            padding: 15px;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border-radius: 8px;
            color: white;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
        }
        
        .stat-text {
            font-size: 11px;
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #0369a1;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        
        tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        
        .badge-hadir { background: #d1fae5; color: #065f46; }
        .badge-izin { background: #fef3c7; color: #92400e; }
        .badge-sakit { background: #fee2e2; color: #991b1b; }
        .badge-alpha { background: #f3f4f6; color: #374151; }
        
        .terlambat-badge {
            background: #fae8ff;
            color: #7c3aed;
            font-size: 9px;
            margin-left: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #64748b;
            font-size: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            height: 60px;
            margin-bottom: 5px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #0369a1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .print-btn:hover {
            background: #075985;
        }
        
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Print / Save PDF</button>
    
    <div class="header">
        <h1>LAPORAN KEHADIRAN BULANAN</h1>
        <h2>SMK YAPIM BIRU BIRU - Periode {{ $namaBulan }} {{ $tahun }}</h2>
    </div>
    
    <div class="info-section">
        <div class="info-left">
            <div class="info-row">
                <span class="info-label">Nama Guru</span>
                <span class="info-value">: {{ $user->nama_lengkap ?? $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIP</span>
                <span class="info-value">: {{ $guru->nip ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">: {{ $user->email }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-row">
                <span class="info-label">Periode</span>
                <span class="info-value">: {{ $namaBulan }} {{ $tahun }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Hari Kerja</span>
                <span class="info-value">: {{ $hariKerja }} hari</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Cetak</span>
                <span class="info-value">: {{ now()->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>
    
    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-number">{{ $totalHadir }}</div>
            <div class="stat-text">Hadir</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $totalIzin }}</div>
            <div class="stat-text">Izin</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $totalSakit }}</div>
            <div class="stat-text">Sakit</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $totalAlpha }}</div>
            <div class="stat-text">Alpha</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $persentaseKehadiran }}%</div>
            <div class="stat-text">Persentase</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 100px;">Tanggal</th>
                <th style="width: 80px;">Hari</th>
                <th style="width: 80px;">Jam Masuk</th>
                <th style="width: 80px;">Jam Keluar</th>
                <th style="width: 80px;">Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensi as $index => $p)
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
                        <span class="badge terlambat-badge">Terlambat</span>
                    @endif
                </td>
                <td>{{ $p->jam_keluar ?? '-' }}</td>
                <td>
                    <span class="badge badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td>{{ $p->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">
                    Tidak ada data presensi untuk periode ini
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="signature-section">
        <div class="signature-box">
            <p style="margin-bottom: 5px;">Mengetahui,</p>
            <p style="margin-bottom: 5px;">Kepala Sekolah</p>
            <div class="signature-line"></div>
            <p>(_____________________)</p>
            <p style="font-size: 10px; color: #64748b;">NIP. .....................</p>
        </div>
    </div>
    
    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Informasi Manajemen Akademik</p>
        <p>© {{ date('Y') }} - SMK YAPIM BIRU BIRU</p>
    </div>
</body>
</html>
