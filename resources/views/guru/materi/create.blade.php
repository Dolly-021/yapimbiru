@extends('layouts.dashboard')

@section('content')
<div class="content-section" style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Upload Materi Baru</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Upload materi pembelajaran untuk siswa</p>
        </div>
        <a href="{{ route('guru.materi') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <strong>Terdapat kesalahan:</strong>
            </div>
            <ul style="list-style: disc; list-style-position: inside; margin-left: 1.75rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data" style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
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

            <!-- Mata Pelajaran -->
            <div>
                <label for="mata_pelajaran" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Mata Pelajaran <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="mata_pelajaran" id="mata_pelajaran" value="{{ old('mata_pelajaran') }}" required maxlength="100" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;" placeholder="Contoh: Matematika, Bahasa Indonesia, IPA">
                @error('mata_pelajaran')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul -->
            <div>
                <label for="judul" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Judul Materi <span style="color: #dc2626;">*</span>
                </label>
                <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required maxlength="200" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s;" placeholder="Contoh: Pengenalan Algoritma Pemrograman">
                @error('judul')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    Deskripsi (Opsional)
                </label>
                <textarea name="deskripsi" id="deskripsi" rows="4" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.95rem; transition: all 0.2s; font-family: inherit;" placeholder="Jelaskan tentang materi yang akan diupload...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="file" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                    File Materi (Opsional)
                </label>
                <div style="border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s;" id="file-drop-area">
                    <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.ppt,.pptx" style="display: none;" onchange="updateFileName(this)">
                    <label for="file" style="cursor: pointer; display: block;">
                        <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p style="color: #4b5563; font-weight: 500; margin-bottom: 0.25rem;">Klik untuk upload atau drag & drop</p>
                        <p style="font-size: 0.875rem; color: #6b7280;">PDF, DOC, DOCX, PPT, PPTX (Max. 10MB)</p>
                        <p id="file-name" style="font-size: 0.875rem; color: #0369a1; font-weight: 600; margin-top: 0.75rem; display: none;"></p>
                    </label>
                </div>
                @error('file')
                    <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #dc2626;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb;">
            <a href="{{ route('guru.materi') }}" class="btn btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Materi
            </button>
        </div>
    </form>
</div>

<style>
#id_kelas:focus, #mata_pelajaran:focus, #judul:focus, #deskripsi:focus {
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
