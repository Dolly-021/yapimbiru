@extends('layouts.dashboard')

@section('content')
<div class="content-section" style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Detail Tugas</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Lihat detail tugas dan kumpulkan jawaban Anda</p>
        </div>
        <a href="{{ route('siswa.tugas') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                <strong>{{ session('success') }}</strong>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                <strong>{{ session('error') }}</strong>
            </div>
        </div>
    @endif

    @php
        $deadline = \Carbon\Carbon::parse($tugas->deadline);
        $now = \Carbon\Carbon::now();
        $isOverdue = $now->gt($deadline);
    @endphp

    <!-- Info Tugas -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem;">
        <div style="border-left: 4px solid #0369a1; padding-left: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
                {{ $tugas->judul_tugas }}
            </h3>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <i class="fas fa-user-tie"></i>
                    <span><strong>Guru:</strong> {{ $tugas->guru->user->nama_lengkap }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <i class="fas fa-school"></i>
                    <span><strong>Kelas:</strong> {{ $tugas->kelas->nama_kelas }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: {{ $isOverdue ? '#dc2626' : '#64748b' }};">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong>Deadline:</strong> {{ $deadline->format('d M Y, H:i') }}</span>
                    @if($isOverdue && !$pengumpulan)
                        <span style="background: #fee2e2; color: #991b1b; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-left: 0.5rem;">Terlambat</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
            <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt"></i>
                Deskripsi & Instruksi
            </h4>
            <p style="color: #475569; line-height: 1.7; white-space: pre-wrap;">{{ $tugas->deskripsi }}</p>
        </div>
    </div>

    <!-- Status Pengumpulan -->
    @if($pengumpulan)
        @php
            $submitTime = \Carbon\Carbon::parse($pengumpulan->waktu_submit);
            $deadline = \Carbon\Carbon::parse($tugas->deadline);
            $isLate = $submitTime->gt($deadline);
            $lateDiff = $isLate ? $submitTime->diff($deadline) : null;
        @endphp
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem; border-left: 4px solid {{ $isLate ? '#dc2626' : '#10b981' }};">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-check-circle" style="color: {{ $isLate ? '#dc2626' : '#10b981' }};"></i>
                Tugas Sudah Dikumpulkan
            </h3>
            <div style="display: grid; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem;">
                    <span style="color: #64748b; font-weight: 500;">Waktu Pengumpulan:</span>
                    <span style="font-weight: 600; color: #1e293b;">{{ $submitTime->format('d M Y, H:i') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem;">
                    <span style="color: #64748b; font-weight: 500;">Status:</span>
                    @if($isLate)
                        <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Terlambat
                            <span style="display: inline-block; margin-left: 0.5rem; font-weight: 600; color: #991b1b; font-size: 0.875rem;">
                                ({{ $lateDiff->d > 0 ? $lateDiff->d . ' hari ' : '' }}{{ $lateDiff->h > 0 ? $lateDiff->h . ' jam ' : '' }}{{ $lateDiff->i > 0 ? $lateDiff->i . ' menit' : '' }})
                            </span>
                        </span>
                    @else
                        <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">Tepat Waktu</span>
                    @endif
                </div>

                <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem;">
                    <span style="color: #64748b; font-weight: 500;">File Jawaban:</span>
                    <a href="{{ asset('storage/' . $pengumpulan->file_jawaban) }}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-download"></i> Download File
                    </a>
                </div>

                @if($pengumpulan->keterangan)
                    <div style="padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem;">
                        <span style="color: #64748b; font-weight: 500; display: block; margin-bottom: 0.5rem;">Keterangan:</span>
                        <p style="color: #1e293b;">{{ $pengumpulan->keterangan }}</p>
                    </div>
                @endif

                @if($pengumpulan->nilai)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; border: 1px solid #fbbf24;">
                        <span style="color: #78350f; font-weight: 500;">Nilai:</span>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #92400e;">{{ $pengumpulan->nilai }}</span>
                    </div>
                @else
                    <div style="padding: 0.75rem; background: #fef3c7; border-radius: 0.5rem; text-align: center; border: 1px solid #fbbf24;">
                        <span style="color: #78350f; font-weight: 500;">
                            <i class="fas fa-hourglass-half"></i> Menunggu penilaian dari guru
                        </span>
                    </div>
                @endif

                @if($pengumpulan->feedback_guru)
                    <div style="padding: 1rem; background: #f0f9ff; border-radius: 0.5rem; border: 1px solid #0369a1;">
                        <span style="color: #0369a1; font-weight: 500; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-comment-dots"></i> Feedback dari Guru:
                        </span>
                        <p style="color: #1e293b; line-height: 1.6;">{{ $pengumpulan->feedback_guru }}</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Form Pengumpulan -->
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-upload"></i>
                Kumpulkan Jawaban
            </h3>

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

            <form action="{{ route('siswa.tugas.submit', $tugas->id_tugas) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="file" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                        File Jawaban <span style="color: #dc2626;">*</span>
                    </label>
                    <div style="border: 2px dashed #d1d5db; border-radius: 0.5rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s;" id="file-drop-area">
                        <input type="file" name="file" id="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar" style="display: none;" onchange="updateFileName(this)">
                        <label for="file" style="cursor: pointer; display: block;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                            <p style="color: #4b5563; font-weight: 500; margin-bottom: 0.25rem;">Klik untuk upload atau drag & drop</p>
                            <p style="font-size: 0.875rem; color: #6b7280;">PDF, DOC, DOCX, PPT, PPTX, ZIP, RAR (Max. 10MB)</p>
                            <p id="file-name" style="font-size: 0.875rem; color: #0369a1; font-weight: 600; margin-top: 0.75rem; display: none;"></p>
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="keterangan" style="display: block; font-size: 0.875rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem;">
                        Keterangan (Opsional)
                    </label>
                    <textarea name="keterangan" id="keterangan" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-family: inherit;" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <a href="{{ route('siswa.tugas') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-paper-plane"></i>
                        Kumpulkan Tugas
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<style>
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
