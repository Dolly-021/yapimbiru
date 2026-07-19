@extends('layouts.dashboard')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-calendar-alt"></i> Jadwal Pelajaran</h2>
    <p>Mengelola jadwal pelajaran (roster) untuk seluruh kelas dan mata pelajaran</p>
</div>

@if(session('success'))
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div style="padding: 1rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
    <i class="fas fa-exclamation-circle" style="font-size: 1.5rem;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Jadwal</h3>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </a>
        </div>
    </div>

    @if($jadwal->count() > 0)
    <!-- Filter Jurusan & Tingkat -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    <i class="fas fa-graduation-cap"></i> Jurusan
                </label>
                <select id="filterJurusan" class="form-control" onchange="updateTingkatOptions(); updateKelasFilter(); filterJadwal()">
                    <option value="">Semua Jurusan</option>
                    <option value="TJKT">TJKT (Teknik Jaringan Komputer)</option>
                    <option value="TKJ">TKJ (Teknik Komputer Jaringan)</option>
                    <option value="TKR">TKR (Teknik Kendaraan Ringan)</option>
                    <option value="TO">TO (Teknik Otomotif)</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    <i class="fas fa-layer-group"></i> Tingkat Kelas
                </label>
                <select id="filterTingkat" class="form-control" onchange="updateKelasFilter(); filterJadwal()">
                    <option value="">Semua Tingkat</option>
                    <option value="X" data-jurusan="TJKT,TO">Kelas 10 (X)</option>
                    <option value="XI" data-jurusan="TKJ,TKR">Kelas 11 (XI)</option>
                    <option value="XII" data-jurusan="TKJ,TKR">Kelas 12 (XII)</option>
                </select>
                <small id="tingkatInfo" style="display: block; margin-top: 0.25rem; color: #ef4444; font-size: 0.85rem;"></small>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                    <i class="fas fa-calendar-day"></i> Hari
                </label>
                <select id="filterHari" class="form-control" onchange="filterJadwal()">
                    <option value="">Semua Hari</option>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>
            </div>
        </div>
        
        <!-- Filter Kelas Spesifik (muncul saat jurusan & tingkat dipilih) -->
        <div id="kelasFilterContainer" style="margin-top: 1rem; display: none;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">
                <i class="fas fa-users"></i> Kelas Spesifik (Opsional)
            </label>
            <div id="kelasCheckboxes" style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                <!-- Checkboxes will be generated here -->
            </div>
            <small style="display: block; margin-top: 0.25rem; color: #64748b; font-size: 0.85rem;">
                Biarkan kosong untuk menampilkan semua kelas
            </small>
        </div>
        
        <div id="filterInfo" style="margin-top: 1rem; padding: 0.75rem; background: #f1f5f9; border-radius: 6px; display: none;">
            <span id="filterText" style="color: #475569; font-weight: 500;"></span>
            <button onclick="resetFilter()" style="background: none; border: none; color: #3b82f6; cursor: pointer; margin-left: 1rem; font-weight: 600;">
                <i class="fas fa-redo"></i> Reset Filter
            </button>
        </div>
    </div>

    @php
        // Group by jurusan, tingkat, hari
        $jadwalGrouped = [];
        foreach($jadwal as $j) {
            // Extract jurusan from nama_kelas (e.g., "X-TKR-1" -> "TKR", "XI-TJKT-2" -> "TJKT")
            $namaKelas = $j->kelas->nama_kelas;
            
            // Improved regex to handle X, XI, XII correctly
            if (preg_match('/^(XII?|X)-(TJKT|TKJ|TKR|TO)/', $namaKelas, $matches)) {
                $tingkat = $matches[1];
                $jurusan = $matches[2];
            } else {
                $tingkat = 'Other';
                $jurusan = 'Other';
            }
            
            if (!isset($jadwalGrouped[$jurusan])) {
                $jadwalGrouped[$jurusan] = [];
            }
            if (!isset($jadwalGrouped[$jurusan][$tingkat])) {
                $jadwalGrouped[$jurusan][$tingkat] = [];
            }
            if (!isset($jadwalGrouped[$jurusan][$tingkat][$j->hari])) {
                $jadwalGrouped[$jurusan][$tingkat][$j->hari] = [];
            }
            
            $jadwalGrouped[$jurusan][$tingkat][$j->hari][] = $j;
        }
        
        $urutanHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    @endphp

    @foreach($jadwalGrouped as $jurusan => $tingkatData)
        @foreach($tingkatData as $tingkat => $hariData)
            @foreach($urutanHari as $hari)
                @if(isset($hariData[$hari]))
                <div class="jadwal-group" data-jurusan="{{ $jurusan }}" data-tingkat="{{ $tingkat }}" data-hari="{{ $hari }}" style="margin-bottom: 2.5rem;">
                    <h3 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem 1.25rem; border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                        <i class="fas fa-calendar-day"></i> 
                        <span>{{ $hari }}</span>
                        <span style="opacity: 0.8;">•</span>
                        <span style="font-size: 0.95rem;">{{ $jurusan }}</span>
                        <span style="opacity: 0.8;">•</span>
                        <span style="font-size: 0.95rem;">Kelas {{ $tingkat }}</span>
                        <span style="margin-left: auto; font-size: 0.9rem; opacity: 0.9;">{{ count($hariData[$hari]) }} jadwal</span>
                    </h3>
                    
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 10%;">Jam</th>
                                    <th style="width: 15%;">Kelas</th>
                                    <th style="width: 18%;">Mata Pelajaran</th>
                                    <th style="width: 22%;">Guru Pengajar</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(collect($hariData[$hari])->sortBy('jam_mulai') as $index => $item)
                                <tr class="jadwal-item" data-kelas="{{ $item->kelas->nama_kelas }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span style="background: #f1f5f9; padding: 0.4rem 0.75rem; border-radius: 6px; display: inline-block; font-weight: 500; font-size: 0.875rem;">
                                            {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $item->kelas->nama_kelas }}</span>
                                    </td>
                                    <td style="font-weight: 600; color: #1e293b;">
                                        {{ $item->mata_pelajaran }}
                                    </td>
                                    <td>
                                        @if($item->guru && $item->guru->user)
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <i class="fas fa-user-circle" style="color: #667eea;"></i>
                                            <div>
                                                <div style="font-weight: 500; color: #2d3748;">{{ $item->guru->user->nama_lengkap }}</div>
                                                <small style="color: #718096;">NIP: {{ $item->guru->nip }}</small>
                                            </div>
                                        </div>
                                        @else
                                        <span style="color: #94a3b8; font-style: italic;">Guru tidak ditemukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <button onclick="openEditModal({{ json_encode($item) }})" class="btn btn-sm" style="background: #3b82f6; color: white; padding: 0.35rem 0.75rem;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.jadwal.delete', $item->id_jadwal) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm" style="background: #ef4444; color: white; padding: 0.35rem 0.75rem;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endforeach
        @endforeach
    @endforeach
    @else
    <div style="padding: 3rem; text-align: center; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-calendar-times" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p style="margin: 0; font-size: 1.1rem;">Belum ada jadwal pelajaran</p>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus"></i> Tambah Jadwal Pertama
        </a>
    </div>
    @endif
</div>

<!-- Edit Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0; color: #1e293b;"><i class="fas fa-edit"></i> Edit Jadwal</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer;">&times;</button>
        </div>
        
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="edit_id_jadwal" name="id_jadwal">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Hari</label>
                <select id="edit_hari" name="hari" class="form-control" required>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Kelas</label>
                <input type="text" id="edit_kelas" class="form-control" readonly style="background: #f1f5f9;">
                <input type="hidden" id="edit_id_kelas" name="id_kelas">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Mata Pelajaran</label>
                <input type="text" id="edit_mata_pelajaran" name="mata_pelajaran" class="form-control" required>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Guru</label>
                <input type="text" id="edit_guru" class="form-control" readonly style="background: #f1f5f9;">
                <input type="hidden" id="edit_id_guru" name="id_guru">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Jam Mulai</label>
                    <input type="time" id="edit_jam_mulai" name="jam_mulai" class="form-control" required>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #374151;">Jam Selesai</label>
                    <input type="time" id="edit_jam_selesai" name="jam_selesai" class="form-control" required>
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn" style="background: #64748b; color: white;">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
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
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

.data-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #1e293b;
    border-bottom: 2px solid #cbd5e1;
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
    color: #475569;
}

.badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-info {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0369a1;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

@media (max-width: 768px) {
    .data-table {
        font-size: 0.85rem;
    }
    
    .data-table th,
    .data-table td {
        padding: 0.75rem 0.5rem;
    }
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

// Extract kelas data from DOM
function getAvailableKelas() {
    const kelasMap = {};
    document.querySelectorAll('.jadwal-group').forEach(group => {
        const jurusan = group.dataset.jurusan;
        const tingkat = group.dataset.tingkat;
        const key = `${tingkat}-${jurusan}`;
        
        if (!kelasMap[key]) {
            kelasMap[key] = new Set();
        }
        
        group.querySelectorAll('.jadwal-item').forEach(item => {
            const namaKelas = item.dataset.kelas;
            if (namaKelas) {
                kelasMap[key].add(namaKelas);
            }
        });
    });
    
    // Convert Sets to sorted Arrays
    Object.keys(kelasMap).forEach(key => {
        kelasMap[key] = Array.from(kelasMap[key]).sort((a, b) => {
            // Sort by number after jurusan (e.g., TO-1, TO-2)
            const numA = parseInt(a.match(/\d+$/)?.[0] || '0');
            const numB = parseInt(b.match(/\d+$/)?.[0] || '0');
            return numA - numB;
        });
    });
    
    return kelasMap;
}

function updateKelasFilter() {
    const jurusan = document.getElementById('filterJurusan').value;
    const tingkat = document.getElementById('filterTingkat').value;
    const container = document.getElementById('kelasFilterContainer');
    const checkboxContainer = document.getElementById('kelasCheckboxes');
    
    // Hide if jurusan or tingkat not selected
    if (!jurusan || !tingkat) {
        container.style.display = 'none';
        return;
    }
    
    // Get available kelas for this combination
    const kelasMap = getAvailableKelas();
    const key = `${tingkat}-${jurusan}`;
    const availableKelas = kelasMap[key] || [];
    
    if (availableKelas.length === 0) {
        container.style.display = 'none';
        return;
    }
    
    // Generate checkboxes
    checkboxContainer.innerHTML = '';
    availableKelas.forEach(namaKelas => {
        const checkbox = document.createElement('label');
        checkbox.style.cssText = 'display: inline-flex; align-items: center; padding: 0.4rem 0.75rem; background: white; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer; transition: all 0.2s; user-select: none;';
        checkbox.onmouseover = function() { this.style.background = '#e0f2fe'; this.style.borderColor = '#3b82f6'; };
        checkbox.onmouseout = function() { 
            if (!this.querySelector('input').checked) {
                this.style.background = 'white'; 
                this.style.borderColor = '#cbd5e1'; 
            }
        };
        
        checkbox.innerHTML = `
            <input type="checkbox" value="${namaKelas}" onchange="handleKelasChange(this)" 
                   style="margin-right: 0.4rem; cursor: pointer;">
            <span style="font-size: 0.9rem; font-weight: 500; color: #1e293b;">${namaKelas}</span>
        `;
        
        checkboxContainer.appendChild(checkbox);
    });
    
    container.style.display = 'block';
}

function handleKelasChange(checkbox) {
    const label = checkbox.parentElement;
    if (checkbox.checked) {
        label.style.background = '#dbeafe';
        label.style.borderColor = '#3b82f6';
    } else {
        label.style.background = 'white';
        label.style.borderColor = '#cbd5e1';
    }
    filterJadwal();
}

function getSelectedKelas() {
    const checkboxes = document.querySelectorAll('#kelasCheckboxes input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

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

function filterJadwal() {
    const jurusan = document.getElementById('filterJurusan').value;
    const tingkat = document.getElementById('filterTingkat').value;
    const hari = document.getElementById('filterHari').value;
    const selectedKelas = getSelectedKelas();
    
    // Validate combination
    if (jurusan && tingkat) {
        const validTingkat = validCombinations[jurusan] || [];
        if (!validTingkat.includes(tingkat)) {
            // Invalid combination, reset tingkat
            document.getElementById('filterTingkat').value = '';
            document.getElementById('tingkatInfo').textContent = `Kombinasi ${jurusan} - Kelas ${tingkat} tidak tersedia`;
            setTimeout(() => { document.getElementById('tingkatInfo').textContent = ''; }, 3000);
            return;
        }
    }
    
    const groups = document.querySelectorAll('.jadwal-group');
    let visibleCount = 0;
    
    groups.forEach(group => {
        const groupJurusan = group.dataset.jurusan;
        const groupTingkat = group.dataset.tingkat;
        const groupHari = group.dataset.hari;
        
        const matchJurusan = !jurusan || groupJurusan === jurusan;
        const matchTingkat = !tingkat || groupTingkat === tingkat;
        const matchHari = !hari || groupHari === hari;
        
        let show = matchJurusan && matchTingkat && matchHari;
        
        // Filter by specific kelas if selected
        if (show && selectedKelas.length > 0) {
            const items = group.querySelectorAll('.jadwal-item');
            let hasVisibleItems = false;
            
            items.forEach(item => {
                const itemKelas = item.dataset.kelas;
                if (selectedKelas.includes(itemKelas)) {
                    item.style.display = '';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            show = hasVisibleItems;
        } else if (show) {
            // Show all items if no specific kelas selected
            group.querySelectorAll('.jadwal-item').forEach(item => {
                item.style.display = '';
            });
        }
        
        if (show) {
            group.style.display = 'block';
            visibleCount++;
        } else {
            group.style.display = 'none';
        }
    });
    
    // Update filter info
    const filterInfo = document.getElementById('filterInfo');
    const filterText = document.getElementById('filterText');
    
    if (jurusan || tingkat || hari || selectedKelas.length > 0) {
        let text = 'Menampilkan jadwal untuk: ';
        const filters = [];
        
        if (jurusan) filters.push(`Jurusan ${jurusan}`);
        if (tingkat) filters.push(`Kelas ${tingkat}`);
        if (selectedKelas.length > 0) filters.push(`Kelas spesifik: ${selectedKelas.join(', ')}`);
        if (hari) filters.push(`Hari ${hari}`);
        
        text += filters.join(' • ');
        text += ` (${visibleCount} kelompok)`;
        
        filterText.textContent = text;
        filterInfo.style.display = 'block';
    } else {
        filterInfo.style.display = 'none';
    }
}

function resetFilter() {
    document.getElementById('filterJurusan').value = '';
    document.getElementById('filterTingkat').value = '';
    document.getElementById('filterHari').value = '';
    document.getElementById('tingkatInfo').textContent = '';
    document.getElementById('kelasFilterContainer').style.display = 'none';
    updateTingkatOptions();
    filterJadwal();
}

function openEditModal(jadwal) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editForm').action = '/admin/jadwal/' + jadwal.id_jadwal;
    document.getElementById('edit_id_jadwal').value = jadwal.id_jadwal;
    document.getElementById('edit_hari').value = jadwal.hari;
    document.getElementById('edit_id_kelas').value = jadwal.id_kelas;
    document.getElementById('edit_kelas').value = jadwal.kelas.nama_kelas;
    document.getElementById('edit_mata_pelajaran').value = jadwal.mata_pelajaran;
    document.getElementById('edit_id_guru').value = jadwal.id_guru;
    document.getElementById('edit_guru').value = jadwal.guru && jadwal.guru.user ? jadwal.guru.user.nama_lengkap : 'Guru tidak ditemukan';
    document.getElementById('edit_jam_mulai').value = jadwal.jam_mulai.substring(0, 5);
    document.getElementById('edit_jam_selesai').value = jadwal.jam_selesai.substring(0, 5);
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
