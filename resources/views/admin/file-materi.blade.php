@extends('layouts.dashboard')

@section('title', 'File Materi')

@section('content')
<div class="welcome-card">
    <h2><i class="fas fa-folder-open"></i> File Materi Pembelajaran</h2>
    <p>Memantau dan mengelola file materi pembelajaran yang diunggah oleh guru</p>
</div>

<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-list"></i> Daftar Materi</h3>
        <div style="color: #718096;">
            Total: <strong>{{ $materi->total() }}</strong> materi
        </div>
    </div>

    @if($materi->count() > 0)
    <!-- Bulk Actions Toolbar -->
    <div id="bulkActionsToolbar" style="display: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; align-items: center; gap: 1rem;">
        <span id="selectedCount" style="font-weight: 600;">0 materi dipilih</span>
        <div style="display: flex; gap: 0.5rem; margin-left: auto;">
            <button onclick="toggleSelectAll()" id="selectAllBtn" class="btn btn-sm" style="background: #8b5cf6; color: white; padding: 0.5rem 1rem;">
                <i class="fas fa-check-double"></i> Pilih Semua
            </button>
            <button onclick="bulkDownload()" class="btn btn-sm" style="background: #10b981; color: white; padding: 0.5rem 1rem;">
                <i class="fas fa-download"></i> Download Terpilih
            </button>
            <button onclick="bulkDelete()" class="btn btn-sm" style="background: #ef4444; color: white; padding: 0.5rem 1rem;">
                <i class="fas fa-trash"></i> Hapus Terpilih
            </button>
            <button onclick="clearSelection()" class="btn btn-sm" style="background: #64748b; color: white; padding: 0.5rem 1rem;">
                <i class="fas fa-times"></i> Batal
            </button>
        </div>
    </div>

    <!-- Card Grid -->
    <div class="materi-grid">
        @foreach($materi as $m)
        <div class="materi-card">
            <!-- Checkbox in top-left corner -->
            <input type="checkbox" class="materi-checkbox" value="{{ $m->id_materi }}" 
                   data-file="{{ $m->file_materi }}" 
                   data-title="{{ $m->judul_materi }}"
                   onchange="updateSelection()" 
                   style="position: absolute; top: 0.75rem; left: 0.75rem; cursor: pointer; width: 20px; height: 20px; z-index: 10;">
            
            <!-- Card Header with gradient -->
            <div class="card-header">
                <i class="fas fa-book" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: white;">{{ $m->judul_materi }}</h4>
                <span class="class-badge">{{ $m->kelas->nama_kelas }}</span>
            </div>
            
            <!-- Card Body -->
            <div class="card-body">
                <div style="margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-tie" style="color: #64748b; font-size: 0.875rem;"></i>
                        <span style="font-size: 0.875rem; font-weight: 600; color: #1e293b;">
                            @if($m->guru && $m->guru->user)
                                {{ $m->guru->user->nama_lengkap }}
                            @else
                                <span style="color: #94a3b8; font-style: italic;">Guru tidak ditemukan</span>
                            @endif
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-book-open" style="color: #64748b; font-size: 0.875rem;"></i>
                        <span style="font-size: 0.875rem; color: #475569;">{{ $m->mata_pelajaran }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-calendar" style="color: #64748b; font-size: 0.875rem;"></i>
                        <span style="font-size: 0.875rem; color: #64748b;">{{ \Carbon\Carbon::parse($m->uploaded_at)->format('d M Y, H:i') }}</span>
                    </div>
                </div>

                @if($m->deskripsi)
                <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 1rem; line-height: 1.5;">
                    {{ Str::limit($m->deskripsi, 80) }}
                </p>
                @endif

                <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                    <button onclick="showDetail({{ $m->id_materi }})" class="btn-detail">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    @if($m->file_materi)
                    <a href="{{ asset('storage/materi/' . $m->file_materi) }}" target="_blank" class="btn-download" title="Download File">
                        <i class="fas fa-download"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $materi->links() }}
    </div>
    @else
    <div style="padding: 3rem; text-align: center; background: #f7fafc; border-radius: 8px; color: #718096;">
        <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
        <p style="margin: 0; font-size: 1.1rem;">Belum ada materi yang diunggah</p>
    </div>
    @endif
</div>

<!-- Modal Detail -->
<div id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #0e7490 0%, #14b8a6 100%); color: white; padding: 1.5rem; border-radius: 12px 12px 0 0;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600;" id="modalTitle">Detail Materi</h3>
        </div>
        <div style="padding: 1.5rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Judul Materi</label>
                <p style="font-size: 1rem; color: #1e293b; margin: 0;" id="modalJudul"></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Mata Pelajaran</label>
                <p style="font-size: 1rem; color: #1e293b; margin: 0;" id="modalMapel"></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Kelas</label>
                <p style="font-size: 1rem; color: #1e293b; margin: 0;" id="modalKelas"></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Guru Pengajar</label>
                <p style="font-size: 1rem; color: #1e293b; margin: 0;" id="modalGuru"></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Deskripsi</label>
                <p style="font-size: 0.875rem; color: #475569; margin: 0; line-height: 1.6;" id="modalDeskripsi"></p>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">Tanggal Upload</label>
                <p style="font-size: 1rem; color: #1e293b; margin: 0;" id="modalTanggal"></p>
            </div>
            <div style="margin-bottom: 1rem;" id="modalFileSection">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">File</label>
                <a id="modalFileLink" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #3b82f6; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    <i class="fas fa-download"></i> Download File
                </a>
            </div>
            <div style="margin-bottom: 1.5rem;" id="modalLinkSection">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Link Eksternal</label>
                <a id="modalLinkEksternal" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #10b981; color: white; border-radius: 6px; text-decoration: none; font-size: 0.875rem;">
                    <i class="fas fa-external-link-alt"></i> Buka Link
                </a>
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button onclick="deleteFromModal()" class="btn" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                    <i class="fas fa-trash"></i> Hapus
                </button>
                <button onclick="closeModal()" class="btn" style="background: #64748b; color: white; padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; font-weight: 500;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.materi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.materi-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
    display: flex;
    flex-direction: column;
}

.materi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-header {
    background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
    color: white;
    padding: 1.5rem 1rem 1rem 3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}

.class-badge {
    background: rgba(255,255,255,0.2);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
    backdrop-filter: blur(10px);
}

.card-body {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.btn-detail {
    flex: 1;
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
    color: white;
    padding: 0.625rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-detail:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(249, 115, 22, 0.3);
}

.btn-download {
    background: #10b981;
    color: white;
    padding: 0.625rem 0.875rem;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-download:hover {
    background: #059669;
}
</style>

<script>
// Store materi data for modal
const materiData = {!! json_encode($materi->mapWithKeys(function($m) {
    return [$m->id_materi => [
        'id_materi' => $m->id_materi,
        'judul_materi' => $m->judul_materi,
        'mata_pelajaran' => $m->mata_pelajaran,
        'kelas' => $m->kelas->nama_kelas ?? '',
        'guru' => $m->guru && $m->guru->user ? $m->guru->user->nama_lengkap : 'Guru tidak ditemukan',
        'deskripsi' => $m->deskripsi,
        'uploaded_at' => $m->uploaded_at ? $m->uploaded_at->format('d M Y, H:i') : '-',
        'file_materi' => $m->file_materi,
        'link_eksternal' => $m->link_eksternal,
    ]];
})) !!};

function showDetail(id) {
    const data = materiData[id];
    if (!data) return;
    
    document.getElementById('modalTitle').textContent = 'Detail Materi';
    document.getElementById('modalJudul').textContent = data.judul_materi;
    document.getElementById('modalMapel').textContent = data.mata_pelajaran;
    document.getElementById('modalKelas').textContent = data.kelas;
    document.getElementById('modalGuru').textContent = data.guru;
    document.getElementById('modalDeskripsi').textContent = data.deskripsi || 'Tidak ada deskripsi';
    document.getElementById('modalTanggal').textContent = data.uploaded_at;
    
    // File section
    const fileSection = document.getElementById('modalFileSection');
    if (data.file_materi) {
        fileSection.style.display = 'block';
        document.getElementById('modalFileLink').href = '/storage/materi/' + data.file_materi;
    } else {
        fileSection.style.display = 'none';
    }
    
    // Link section
    const linkSection = document.getElementById('modalLinkSection');
    if (data.link_eksternal) {
        linkSection.style.display = 'block';
        document.getElementById('modalLinkEksternal').href = data.link_eksternal;
    } else {
        linkSection.style.display = 'none';
    }
    
    // Store current ID for delete
    document.getElementById('detailModal').dataset.currentId = id;
    
    document.getElementById('detailModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

function deleteFromModal() {
    const id = document.getElementById('detailModal').dataset.currentId;
    const data = materiData[id];
    
    if (!confirm(`Yakin ingin menghapus materi "${data.judul_materi}"?`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/file-materi/' + id;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    form.appendChild(methodField);
    
    document.body.appendChild(form);
    form.submit();
}

// Close modal when clicking outside
document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.materi-checkbox');
    const checkedCount = document.querySelectorAll('.materi-checkbox:checked').length;
    const selectAllBtn = document.getElementById('selectAllBtn');
    
    if (checkedCount === checkboxes.length) {
        // Deselect all
        checkboxes.forEach(cb => cb.checked = false);
        selectAllBtn.innerHTML = '<i class="fas fa-check-double"></i> Pilih Semua';
    } else {
        // Select all
        checkboxes.forEach(cb => cb.checked = true);
        selectAllBtn.innerHTML = '<i class="fas fa-times-circle"></i> Batalkan Pilih Semua';
    }
    updateSelection();
}

function updateSelection() {
    const checkboxes = document.querySelectorAll('.materi-checkbox:checked');
    const toolbar = document.getElementById('bulkActionsToolbar');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const allCheckboxes = document.querySelectorAll('.materi-checkbox');
    
    if (checkboxes.length > 0) {
        toolbar.style.display = 'flex';
        selectedCount.textContent = checkboxes.length + ' materi dipilih';
        
        // Update tombol Select All
        if (checkboxes.length === allCheckboxes.length) {
            selectAllBtn.innerHTML = '<i class="fas fa-times-circle"></i> Batalkan Pilih Semua';
        } else {
            selectAllBtn.innerHTML = '<i class="fas fa-check-double"></i> Pilih Semua';
        }
    } else {
        toolbar.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.materi-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllBtn').innerHTML = '<i class="fas fa-check-double"></i> Pilih Semua';
    updateSelection();
}

function bulkDownload() {
    const checkboxes = document.querySelectorAll('.materi-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 materi untuk didownload');
        return;
    }
    
    let filesDownloaded = 0;
    let noFileCount = 0;
    
    checkboxes.forEach((checkbox, index) => {
        const fileName = checkbox.dataset.file;
        if (fileName) {
            setTimeout(() => {
                const link = document.createElement('a');
                link.href = '/storage/materi/' + fileName;
                link.download = fileName;
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, index * 300);
            filesDownloaded++;
        } else {
            noFileCount++;
        }
    });
    
    if (filesDownloaded > 0) {
        alert(`Download dimulai untuk ${filesDownloaded} file.${noFileCount > 0 ? ' (' + noFileCount + ' materi tidak memiliki file)' : ''}`);
    } else {
        alert('Tidak ada file yang bisa didownload dari materi yang dipilih');
    }
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.materi-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 materi untuk dihapus');
        return;
    }
    
    const materiIds = Array.from(checkboxes).map(cb => cb.value);
    const titles = Array.from(checkboxes).map(cb => cb.dataset.title);
    
    const confirmMsg = `Yakin ingin menghapus ${materiIds.length} materi berikut?\n\n${titles.slice(0, 5).join('\n')}${titles.length > 5 ? '\n... dan ' + (titles.length - 5) + ' lainnya' : ''}`;
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.file-materi.bulk-delete") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';
    form.appendChild(methodField);
    
    materiIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'materi_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
