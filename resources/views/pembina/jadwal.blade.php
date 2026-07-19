@extends('layouts.dashboard')

@section('title', 'Jadwal Aktif')

@section('content')
<div class="header-section" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1><i class="fas fa-calendar-alt"></i> Jadwal Pembelajaran Aktif</h1>
        <p>Lihat jadwal pembelajaran semua guru dan kelas</p>
    </div>
    <x-dashboard-button />
</div>

<div class="welcome-card">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Pembelajaran Hari Ini</h2>
    <p>Lihat jadwal pembelajaran yang sedang berlangsung di hari ini ({{ $hariIni }})</p>
</div>

<!-- Filter Jurusan & Tingkat -->
<div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <form method="GET" action="{{ route('pembina.jadwal') }}" id="filterForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    <i class="fas fa-graduation-cap"></i> Jurusan
                </label>
                <select name="jurusan" id="filterJurusan" class="form-control" onchange="updateTingkatOptions(); this.form.submit()">
                    <option value="">Semua Jurusan</option>
                    <option value="TJKT" {{ request('jurusan') == 'TJKT' ? 'selected' : '' }}>TJKT (Teknik Jaringan Komputer)</option>
                    <option value="TKJ" {{ request('jurusan') == 'TKJ' ? 'selected' : '' }}>TKJ (Teknik Komputer Jaringan)</option>
                    <option value="TKR" {{ request('jurusan') == 'TKR' ? 'selected' : '' }}>TKR (Teknik Kendaraan Ringan)</option>
                    <option value="TO" {{ request('jurusan') == 'TO' ? 'selected' : '' }}>TO (Teknik Otomotif)</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    <i class="fas fa-layer-group"></i> Tingkat Kelas
                </label>
                <select name="tingkat" id="filterTingkat" class="form-control" onchange="updateJurusanOptions(); this.form.submit()">
                    <option value="">Semua Tingkat</option>
                    <option value="X" {{ request('tingkat') == 'X' ? 'selected' : '' }}>Kelas 10 (X)</option>
                    <option value="XI" {{ request('tingkat') == 'XI' ? 'selected' : '' }}>Kelas 11 (XI)</option>
                    <option value="XII" {{ request('tingkat') == 'XII' ? 'selected' : '' }}>Kelas 12 (XII)</option>
                </select>
                <small id="tingkatInfo" style="display: block; margin-top: 0.25rem; color: #ef4444; font-size: 0.85rem;"></small>
            </div>
        </div>
        
        @if(request('jurusan') || request('tingkat'))
        <div style="margin-top: 1rem; padding: 0.75rem; background: #f1f5f9; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #475569; font-weight: 500;">
                Filter aktif: 
                @if(request('jurusan')) {{ request('jurusan') }} @endif
                @if(request('jurusan') && request('tingkat')) • @endif
                @if(request('tingkat')) Kelas {{ request('tingkat') }} @endif
            </span>
            <a href="{{ route('pembina.jadwal') }}" style="background: none; border: none; color: #3b82f6; cursor: pointer; text-decoration: none; font-weight: 600;">
                <i class="fas fa-redo"></i> Reset Filter
            </a>
        </div>
        @endif
    </form>
</div>

@if($jadwal->count() > 0)
<div style="background: #e0f2fe; border-left: 4px solid #0369a1; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
    <h3 style="color: #0369a1; margin-top: 0;"><i class="fas fa-calendar-day"></i> Jadwal Hari Ini ({{ $hariIni }})</h3>
    <p style="color: #06b6d4; margin: 0.5rem 0 0 0;">{{ $jadwal->count() }} kelas sedang berlangsung</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    @foreach($jadwal as $j)
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 4px solid #22c55e;">
        <h4 style="color: #2d3748; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-book"></i> {{ $j->mata_pelajaran ?? 'N/A' }}
        </h4>
        <p style="color: #718096; margin: 0.5rem 0;"><strong>Kelas:</strong> {{ $j->kelas->nama_kelas ?? 'N/A' }}</p>
        <p style="color: #718096; margin: 0.5rem 0;"><strong>Guru:</strong> {{ $j->guru?->user?->nama_lengkap ?? 'N/A' }}</p>
        <p style="color: #718096; margin: 0.5rem 0;"><strong>Jam:</strong> {{ date('H:i', strtotime($j->jam_mulai)) }} - {{ date('H:i', strtotime($j->jam_selesai)) }}</p>
    </div>
    @endforeach
</div>
@else
<div style="text-align: center; padding: 3rem; color: #6b7280; background: white; border-radius: 10px;">
    <i class="fas fa-calendar-times" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.3;"></i>
    <p>Tidak ada jadwal pembelajaran untuk hari ini</p>
</div>
@endif

<style>
    .welcome-card {
        background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(3, 105, 161, 0.2);
    }

    .welcome-card h2 {
        margin: 0 0 0.5rem 0;
        font-size: 1.8rem;
    }

    .welcome-card p {
        margin: 0.5rem 0 0 0;
        font-size: 0.95rem;
        opacity: 0.95;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0369a1;
        box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
    }
</style>

<script>
// Kombinasi yang valid: TJKT & TO untuk kelas X, TKJ & TKR untuk kelas XI & XII
const validCombinations = {
    'TJKT': ['X'],
    'TO': ['X'],
    'TKJ': ['XI', 'XII'],
    'TKR': ['XI', 'XII']
};

// Reverse mapping untuk update jurusan berdasarkan tingkat
const tingkatToJurusan = {
    'X': ['TJKT', 'TO'],
    'XI': ['TKJ', 'TKR'],
    'XII': ['TKJ', 'TKR']
};

function updateTingkatOptions() {
    const jurusan = document.getElementById('filterJurusan').value;
    const tingkatSelect = document.getElementById('filterTingkat');
    const tingkatInfo = document.getElementById('tingkatInfo');
    const currentTingkat = tingkatSelect.value;
    
    // Reset all options
    Array.from(tingkatSelect.options).forEach(option => {
        if (option.value !== '') {
            option.disabled = false;
            option.style.display = '';
        }
    });
    
    if (jurusan) {
        const validTingkat = validCombinations[jurusan] || [];
        
        // Disable invalid options
        Array.from(tingkatSelect.options).forEach(option => {
            if (option.value !== '' && !validTingkat.includes(option.value)) {
                option.disabled = true;
                option.style.display = 'none';
            }
        });
        
        // Reset tingkat if current selection is invalid
        if (currentTingkat && !validTingkat.includes(currentTingkat)) {
            tingkatSelect.value = '';
            tingkatInfo.textContent = `Tingkat yang dipilih tidak tersedia untuk ${jurusan}`;
            setTimeout(() => { tingkatInfo.textContent = ''; }, 3000);
        } else {
            tingkatInfo.textContent = '';
        }
    } else {
        tingkatInfo.textContent = '';
    }
}

function updateJurusanOptions() {
    const tingkat = document.getElementById('filterTingkat').value;
    const jurusanSelect = document.getElementById('filterJurusan');
    const currentJurusan = jurusanSelect.value;
    
    // Reset all options
    Array.from(jurusanSelect.options).forEach(option => {
        if (option.value !== '') {
            option.disabled = false;
            option.style.display = '';
        }
    });
    
    if (tingkat) {
        const validJurusan = tingkatToJurusan[tingkat] || [];
        
        // Disable invalid options
        Array.from(jurusanSelect.options).forEach(option => {
            if (option.value !== '' && !validJurusan.includes(option.value)) {
                option.disabled = true;
                option.style.display = 'none';
            }
        });
        
        // Reset jurusan if current selection is invalid
        if (currentJurusan && !validJurusan.includes(currentJurusan)) {
            jurusanSelect.value = '';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTingkatOptions();
    updateJurusanOptions();
});
</script>
@endsection