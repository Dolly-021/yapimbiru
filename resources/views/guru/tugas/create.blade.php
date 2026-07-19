@extends('layouts.dashboard')

@section('content')
<div class="content-section" style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Buat Tugas Baru</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Buat tugas untuk siswa dengan deadline dan instruksi yang jelas</p>
        </div>
        <a href="{{ route('guru.tugas') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                <strong>Terdapat kesalahan:</strong>
            </div>
            <ul style="list-style: disc; list-style-position: inside; margin-left: 1.75rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guru.tugas.store') }}" method="POST" enctype="multipart/form-data" style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
        @csrf
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Kelas -->
            <div>
                <label for="id_kelas" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Kelas <span style="color: #dc2626;">*</span>
                </label>
                <select name="id_kelas" id="id_kelas" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;">
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('id_kelas')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul Tugas -->
            <div>
                <label for="judul_tugas" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Judul Tugas <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="judul_tugas" id="judul_tugas" value="{{ old('judul_tugas') }}" required maxlength="200" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;" placeholder="Contoh: Tugas Algoritma Sorting">
                @error('judul_tugas')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi/Instruksi -->
            <div>
                <label for="deskripsi" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Deskripsi & Instruksi <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="6" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s; font-family: inherit;" placeholder="Jelaskan detail tugas dan instruksi pengerjaan...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deadline -->
            <div>
                <label for="deadline" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Deadline <span style="color: #dc2626;">*</span>
                </label>
                <input type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline') }}" required style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;">
                @error('deadline')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload (Opsional) -->
            <div>
                <label for="file" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    File Lampiran (Opsional)
                </label>
                <div style="border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s;" id="file-drop-area">
                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar" style="display: none;" onchange="updateFileName(this)">
                    <label for="file" style="cursor: pointer; display: block;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                        <p style="color: #4b5563; font-weight: 500; margin-bottom: 0.25rem;">Klik untuk upload atau drag & drop</p>
                        <p style="font-size: 0.875rem; color: #6b7280;">PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR (Max. 10MB)</p>
                        <p id="file-name" style="font-size: 0.875rem; color: #0369a1; font-weight: 600; margin-top: 0.75rem; display: none;"></p>
                    </label>
                </div>
                @error('file')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('guru.tugas') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-paper-plane"></i>
                Buat Tugas
            </button>
        </div>
    </form>
</div>

<style>
#id_kelas:focus, #judul_tugas:focus, #deskripsi:focus, #deadline:focus {
    outline: none;
    border-color: #0369a1;
    box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
}

#file-drop-area:hover {
    border-color: #0369a1;
    background-color: #f0f9ff;
}
</style>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    
    if (fileName) {
        fileNameDisplay.textContent = '✓ ' + fileName;
        fileNameDisplay.style.display = 'block';
    } else {
        fileNameDisplay.style.display = 'none';
    }
}

// Drag and drop functionality
const dropArea = document.getElementById('file-drop-area');
const fileInput = document.getElementById('file');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => {
        dropArea.style.borderColor = '#0369a1';
        dropArea.style.backgroundColor = '#f0f9ff';
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => {
        dropArea.style.borderColor = '#d1d5db';
        dropArea.style.backgroundColor = 'transparent';
    });
});

dropArea.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        fileInput.files = files;
        updateFileName(fileInput);
    }
});
</script>
@endsection
