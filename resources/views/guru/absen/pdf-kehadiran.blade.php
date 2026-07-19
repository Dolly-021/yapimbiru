<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Bulanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9px;
            line-height: 1.2;
            color: #333;
            padding: 15px 10px;
        }
        .header-title {
            text-align: center;
            margin-bottom: 5px;
        }
        .header-title .main-title {
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }
        .header-title .sub-title {
            font-size: 8px;
            margin-bottom: 8px;
        }
        .line {
            border-bottom: 2px solid #333;
            margin-bottom: 5px;
        }
        .info-section {
            margin-bottom: 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-column {
            font-size: 8px;
        }
        .info-row {
            display: grid;
            grid-template-columns: 100px 1fr;
            margin-bottom: 1px;
        }
        .info-label {
            font-weight: normal;
        }
        .info-value {
            text-align: left;
            word-break: break-all;
        }
        .stats-section {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .stat-box {
            text-align: center;
            border: 1px solid #999;
            padding: 4px 2px;
        }
        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 7px;
            color: #666;
            margin-top: 1px;
            font-weight: normal;
        }
        .table-section {
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .table-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        th {
            background-color: #f5f5f5;
            border: 1px solid #999;
            padding: 3px 2px;
            text-align: center;
            font-weight: bold;
            font-size: 7px;
        }
        td {
            border: 1px solid #999;
            padding: 2px 2px;
            text-align: center;
        }
        td.text-left {
            text-align: left;
            padding-left: 6px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header-title">
        <div class="main-title">LAPORAN KEHADIRAN BULANAN</div>
        <div class="sub-title">
            SMK YAPIM BIRU BIRU - Periode {{ date('F Y', strtotime($bulanFilter)) }}
            @if($kelasFilter)
                <br/><span style="font-size: 9px;">(Kelas: @foreach($kelas as $k) @if($k->id_kelas == $kelasFilter) {{ $k->nama_kelas }} @endif @endforeach)</span>
            @endif
        </div>
    </div>
    <div class="line"></div>

    <!-- INFO SECTION -->
    <div class="info-section">
        <!-- Left Column: Guru Info -->
        <div class="info-column">
            <div class="info-row">
                <span class="info-label">Nama Guru</span>
                <span class="info-value">: {{ $user->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIP</span>
                <span class="info-value">: -</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">: {{ $user->email ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Right Column: Period Info -->
        <div class="info-column">
            <div class="info-row">
                <span class="info-label">Periode</span>
                <span class="info-value">: {{ date('F Y', strtotime($bulanFilter)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas</span>
                <span class="info-value">: @if($kelasFilter) @foreach($kelas as $k) @if($k->id_kelas == $kelasFilter) {{ $k->nama_kelas }} @endif @endforeach @else Semua Kelas @endif</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Cetak</span>
                <span class="info-value">: {{ date('d/m/Y H:i', strtotime(now())) }}</span>
            </div>
        </div>
    </div>

    <!-- STATISTICS SECTION -->
    @if($absens->count() > 0)
        @php
            $totalHadir = 0;
            $totalIzin = 0;
            $totalSakit = 0;
            $totalTidakHadir = 0;
            foreach($absens as $absen) {
                $totalHadir += $absen->absenSiswas->where('status', 'hadir')->count();
                $totalIzin += $absen->absenSiswas->where('status', 'izin')->count();
                $totalSakit += $absen->absenSiswas->where('status', 'sakit')->count();
                $totalTidakHadir += $absen->absenSiswas->where('status', 'tidak_hadir')->count();
            }
            $totalSiswa = $absens->count() > 0 ? $absens->first()->absenSiswas->count() : 0;
        @endphp

        <div class="stats-section">
            <div class="stat-box">
                <div class="stat-number">{{ $totalHadir }}</div>
                <div class="stat-label">Hadir</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalTidakHadir }}</div>
                <div class="stat-label">Tidak Hadir</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalIzin }}</div>
                <div class="stat-label">Izin</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $totalSakit }}</div>
                <div class="stat-label">Sakit</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">
                    @php
                        $totalAll = $totalHadir + $totalTidakHadir + $totalIzin + $totalSakit;
                        $persenHadir = $totalAll > 0 ? round(($totalHadir / $totalAll) * 100, 1) : 0;
                    @endphp
                    {{ $persenHadir }}%
                </div>
                <div class="stat-label">Kehadiran</div>
            </div>
        </div>

        <!-- TABLE SECTION - DETAIL SISWA -->
        <div class="table-section">
            <div style="font-size: 9px; font-weight: bold; margin-bottom: 3px;">DETAIL KEHADIRAN SISWA</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 35%;">Nama Siswa</th>
                        <th style="width: 15%;">Hadir</th>
                        <th style="width: 15%;">Izin</th>
                        <th style="width: 15%;">Sakit</th>
                        <th style="width: 16%;">Tidak Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Count attendance per student
                        $siswaStats = [];
                        foreach($allSiswa as $siswa) {
                            $siswaStats[$siswa->id_siswa] = [
                                'hadir' => 0,
                                'izin' => 0,
                                'sakit' => 0,
                                'tidak_hadir' => 0
                            ];
                            
                            foreach($absens as $absen) {
                                $absenSiswa = $absen->absenSiswas->where('id_siswa', $siswa->id_siswa)->first();
                                if($absenSiswa) {
                                    $siswaStats[$siswa->id_siswa][$absenSiswa->status]++;
                                }
                            }
                        }
                    @endphp
                    
                    @forelse($allSiswa as $idx => $siswa)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td class="text-left">{{ $siswa->user?->name ?? 'N/A' }}</td>
                            <td>{{ $siswaStats[$siswa->id_siswa]['hadir'] ?? 0 }}</td>
                            <td>{{ $siswaStats[$siswa->id_siswa]['izin'] ?? 0 }}</td>
                            <td>{{ $siswaStats[$siswa->id_siswa]['sakit'] ?? 0 }}</td>
                            <td>{{ $siswaStats[$siswa->id_siswa]['tidak_hadir'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 10px;">Tidak ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- FOOTER -->
        <div style="margin-top: 8px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; font-size: 8px;">
                <div style="text-align: center;">
                    <div style="margin-bottom: 25px; font-weight: bold;">Mengetahui,</div>
                    <div style="border-top: 1px solid #333; padding-top: 1px;">_________________</div>
                </div>
                <div style="text-align: center;">
                    <div style="margin-bottom: 25px; font-weight: bold;">Kepala Sekolah</div>
                    <div style="border-top: 1px solid #333; padding-top: 1px;">_________________</div>
                </div>
            </div>
        </div>

        <div style="font-size: 7px; color: #666; margin-top: 8px; text-align: center; line-height: 1.1;">
            Dokumen ini digenerate oleh Sistem Informasi Manajemen Akademik<br/>
            © 2025 - SMK YAPIM BIRU BIRU
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Tidak ada data kehadiran untuk periode {{ date('F Y', strtotime($bulanFilter)) }}</p>
        </div>
    @endif
</body>
</html>
