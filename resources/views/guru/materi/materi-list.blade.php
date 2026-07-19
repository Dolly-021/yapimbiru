@extends('layouts.dashboard')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Materi Pembelajaran</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Kelola materi pembelajaran untuk siswa</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('guru.materi.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Upload Materi Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center;">
            <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($materi->count() > 0)
        <!-- Bulk Actions Toolbar -->
        <div id="bulkActionsToolbar" style="display: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; align-items: center; gap: 1rem;">
            <span id="selectedCount" style="font-weight: 600;">0 materi dipilih</span>
            <div style="display: flex; gap: 0.5rem; margin-left: auto;">
                <button onclick="toggleSelectAll()" id="selectAllBtn" class="btn btn-sm" style="background: #8b5cf6; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer;">
                    <i class="fas fa-check-double"></i> Pilih Semua
                </button>
                <button onclick="bulkDownload()" class="btn btn-sm" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer;">
                    <i class="fas fa-download"></i> Download Terpilih
                </button>
                <button onclick="bulkDelete()" class="btn btn-sm" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer;">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
                <button onclick="clearSelection()" class="btn btn-sm" style="background: #64748b; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.375rem; cursor: pointer;">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
            @foreach($materi as $m)
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; border-top: 4px solid #0369a1; transition: all 0.3s; position: relative;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.12)'" onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                <!-- Checkbox di pojok kiri atas -->
                <div style="position: absolute; top: 0.75rem; left: 0.75rem; z-index: 10;">
                    <input type="checkbox" class="materi-checkbox" value="{{ $m->id_materi }}" 
                           data-file="{{ $m->file_materi }}" 
                           data-title="{{ $m->judul_materi }}"
                           onchange="updateSelection()" 
                           style="cursor: pointer; width: 20px; height: 20px; accent-color: #f59e0b;">
                </div>
                
                <!-- Header dengan icon -->
                <div style="background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%); padding: 1.25rem 1.25rem 1.25rem 3rem; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    <div style="position: relative; z-index: 1;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                            <div style="background: rgba(255,255,255,0.2); padding: 0.5rem; border-radius: 0.5rem; display: inline-flex;">
                                <i class="fas fa-book" style="color: white; font-size: 1.25rem;"></i>
                            </div>
                            <span style="background: rgba(255,255,255,0.2); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                {{ $m->kelas->nama_kelas }}
                            </span>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: 700; color: white; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.8rem;">
                            {{ $m->judul_materi }}
                        </h3>
                    </div>
                </div>
                
                <!-- Body -->
                <div style="padding: 1.25rem;">
                    @if($m->deskripsi)
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 1rem 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; min-height: 3.9rem;">
                        {{ $m->deskripsi }}
                    </p>
                    @else
                    <p style="font-size: 0.875rem; color: #9ca3af; margin: 0 0 1rem 0; font-style: italic; min-height: 3.9rem;">
                        Tidak ada deskripsi
                    </p>
                    @endif
                    
                    <!-- Info footer -->
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: #f9fafb; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-calendar-alt" style="color: #6b7280; font-size: 0.875rem;"></i>
                            <span style="font-size: 0.75rem; color: #6b7280;">
                                {{ \Carbon\Carbon::parse($m->uploaded_at)->format('d M Y') }}
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-file-pdf" style="color: #dc2626; font-size: 0.875rem;"></i>
                            <span style="font-size: 0.75rem; color: #6b7280;">PDF</span>
                        </div>
                    </div>
                    
                    <button onclick="showDetail({{ $m->id_materi }})" style="width: 100%; padding: 0.75rem; font-size: 0.875rem; font-weight: 600; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white; border: none; border-radius: 0.5rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 2px 4px rgba(245,158,11,0.3);" onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 8px rgba(245,158,11,0.4)'" onmouseout="this.style.transform=''; this.style.boxShadow='0 2px 4px rgba(245,158,11,0.3)'">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $materi->links() }}
        </div>

        <!-- Modal Detail -->
        @foreach($materi as $m)
        <div id="modal-{{ $m->id_materi }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
            <div style="background: white; border-radius: 0.75rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
                <div style="background: linear-gradient(to right, #0e7490, #14b8a6); padding: 1.5rem; position: relative;">
                    <h2 style="color: white; margin: 0; font-size: 1.5rem; padding-right: 2rem;">Detail Materi</h2>
                    <button onclick="closeModal({{ $m->id_materi }})" style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); border: none; color: white; font-size: 1.5rem; width: 2rem; height: 2rem; border-radius: 0.375rem; cursor: pointer; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        &times;
                    </button>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Judul Materi</label>
                        <p style="color: #111827; margin: 0; font-size: 1rem;">{{ $m->judul_materi }}</p>
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Guru Pengajar</label>
                        <p style="color: #111827; margin: 0;">{{ $m->guru->nama_guru }}</p>
                    </div>
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Kelas</label>
                        <span style="padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 600; border-radius: 9999px; background: #dbeafe; color: #1e40af; display: inline-block;">
                            {{ $m->kelas->nama_kelas }}
                        </span>
                    </div>
                    
                    @if($m->deskripsi)
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Deskripsi</label>
                        <p style="color: #6b7280; margin: 0; line-height: 1.6;">{{ $m->deskripsi }}</p>
                    </div>
                    @endif
                    
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">File</label>
                        <div style="background: #f3f4f6; padding: 0.75rem; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-file-pdf" style="color: #dc2626; font-size: 1.25rem;"></i>
                            <span style="color: #374151; font-size: 0.875rem; flex: 1; word-break: break-all;">{{ $m->file_materi }}</span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tanggal Upload</label>
                        <p style="color: #111827; margin: 0;">
                            <i class="fas fa-calendar-alt" style="color: #6b7280; margin-right: 0.5rem;"></i>
                            {{ $m->uploaded_at->format('d F Y, H:i') }} WIB
                        </p>
                    </div>
                    
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; display: flex; gap: 0.75rem;">
                        <a href="{{ asset('storage/materi/' . $m->file_materi) }}" target="_blank" class="btn btn-primary" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem;">
                            <i class="fas fa-download"></i> Download
                        </a>
                        <button onclick="confirmDelete({{ $m->id_materi }})" style="flex: 1; background: #dc2626; color: white; border: none; padding: 0.75rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#991b1b'" onmouseout="this.style.background='#dc2626'">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                        <form id="delete-form-{{ $m->id_materi }}" action="{{ route('guru.materi.delete', $m->id_materi) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 3rem; text-align: center;">
            <svg style="width: 4rem; height: 4rem; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;">Belum Ada Materi</h3>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">Mulai upload materi pembelajaran untuk siswa Anda</p>
            <a href="{{ route('guru.materi.create') }}" class="btn btn-primary">
                Upload Materi Pertama
            </a>
        </div>
    @endif
</div>

<script>
function showDetail(id) {
    document.getElementById('modal-' + id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById('modal-' + id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.id && event.target.id.startsWith('modal-')) {
        closeModal(event.target.id.replace('modal-', ''));
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
    const count = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const allCheckboxes = document.querySelectorAll('.materi-checkbox');
    
    if (checkboxes.length > 0) {
        toolbar.style.display = 'flex';
        count.textContent = checkboxes.length + ' materi dipilih';
        
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
        alert('Pilih minimal 1 materi');
        return;
    }
    
    checkboxes.forEach(cb => {
        const file = cb.dataset.file;
        const link = document.createElement('a');
        link.href = '{{ asset("storage/materi") }}/' + file;
        link.download = file;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.materi-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Pilih minimal 1 materi');
        return;
    }
    
    Swal.fire({
        title: 'Hapus ' + checkboxes.length + ' Materi?',
        text: 'File akan dihapus secara permanen dan tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = Array.from(checkboxes).map(cb => cb.value);
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("guru.materi.bulk-delete") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = JSON.stringify(ids);
            form.appendChild(idsInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Materi?',
        text: 'File akan dihapus secara permanen dan tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection
