@extends('layouts.dashboard')

@section('title', 'Ajukan Izin')

@section('content')
<div class="content-section">
    <!-- Header dengan Tombol Kembali -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 class="section-title" style="margin: 0;"><i class="fas fa-file-medical"></i> Ajukan Izin</h3>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    

    <p style="color: #6b7280; margin-bottom: 1.5rem;">Ajukan izin atau surat sakit untuk ketidakhadiran Anda. Bukti (surat sakit atau surat izin) bersifat opsional, dapat diunggah dalam format PDF atau gambar (JPG/PNG) dengan ukuran maksimal 5 MB.</p>

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #dc2626; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
            <strong>Terjadi Kesalahan:</strong>
            <ul style="margin-top: 0.5rem; margin-bottom: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('siswa.izin.submit') }}" method="POST" enctype="multipart/form-data" id="form-ajukan-izin">
        @csrf

        <!-- Pilihan Tipe (Sakit / Izin) -->
        <div style="background: #f7fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.75rem; font-weight: 600; color: #2d3748;">Tipe Ketidakhadiran</label>
            <div style="display: flex; gap: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="tipe" value="sakit" required id="tipe-sakit" style="cursor: pointer;">
                    <span style="font-weight: 500;">Sakit</span>
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="radio" name="tipe" value="izin" required id="tipe-izin" style="cursor: pointer;">
                    <span style="font-weight: 500;">Izin</span>
                </label>
            </div>
            @error('tipe')<p style="color: #dc2626; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <!-- Tanggal Izin -->
        <div style="margin-bottom: 1.5rem;">
            <label for="tanggal" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">Tanggal Izin</label>
            <input type="date" id="tanggal" name="tanggal" required
                   value="{{ old('tanggal') }}"
                   min="{{ date('Y-m-d') }}"
                   style="width: 100%; padding: 0.85rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem; background: white; cursor: pointer;">
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 0.25rem;">Pilih tanggal ketidakhadiran, hari akan otomatis terdeteksi</p>
            @error('tanggal')<p style="color: #dc2626; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <!-- Hari (Auto-detected, hidden) -->
        <input type="hidden" id="hari" name="hari" value="{{ old('hari') }}">
        
        <!-- Info Hari Terdeteksi -->
        <div id="hari-info" style="margin-bottom: 1.5rem; padding: 1rem; background: #e0f2fe; border-left: 4px solid #0369a1; border-radius: 6px; display: none;">
            <p style="margin: 0; color: #0c4a6e; font-weight: 600;">
                <i class="fas fa-calendar-check"></i> Hari : <span id="hari-text"></span>
            </p>
        </div>
        
        <!-- Notifikasi Tidak Ada Jadwal -->
        <div id="no-jadwal-warning" style="margin-bottom: 1.5rem; padding: 1rem; background: #fee2e2; border-left: 4px solid #dc2626; border-radius: 6px; display: none;">
            <p style="margin: 0; color: #991b1b; font-weight: 600;">
                <i class="fas fa-exclamation-triangle"></i> <span id="no-jadwal-text">Tidak ada jadwal pelajaran di hari ini</span>
            </p>
            <p style="margin: 0.5rem 0 0 0; color: #7f1d1d; font-size: 0.9rem;">
                Silakan pilih tanggal lain yang memiliki jadwal pelajaran.
            </p>
        </div>

        <!-- Pilih Jadwal (Jam Pelajaran) -->
        <div id="jadwal-container" style="margin-bottom: 1.5rem; display: none;">
            <label for="id_jadwal" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                Jam Pelajaran <span style="color: #dc2626;">*</span>
            </label>
            <select id="id_jadwal" name="id_jadwal" required
                    style="width: 100%; padding: 0.85rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem; background: white; cursor: pointer;">
                <option value="">-- Pilih Jam Pelajaran --</option>
            </select>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 0.25rem;">Izin akan diajukan ke guru yang mengajar pada jam ini</p>
            @error('id_jadwal')<p style="color: #dc2626; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <!-- Alasan (muncul hanya jika tipe "Izin") -->
        <div id="alasan-container" style="display: none; margin-bottom: 1.5rem;">
            <label for="alasan" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                Alasan Izin <span style="color: #dc2626;">*</span>
            </label>
            <textarea id="alasan" name="alasan" 
                      placeholder="Jelaskan alasan izin Anda (contoh: Acara keluarga, Keperluan mendesak, dll.)"
                      maxlength="500"
                      style="width: 100%; padding: 0.85rem; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem; resize: vertical; min-height: 100px;">{{ old('alasan') }}</textarea>
            <p style="color: #6b7280; font-size: 0.85rem; margin-top: 0.25rem;">Wajib diisi jika tipe izin. Maksimal 500 karakter</p>
            @error('alasan')<p style="color: #dc2626; font-size: 0.85rem; margin-top: 0.25rem;">{{ $message }}</p>@enderror
        </div>

        <!-- Upload Bukti -->
        <div style="margin-bottom: 1.5rem;">
            <label for="bukti_file" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">
                <span id="bukti-label">Upload Bukti <span style="color: #6b7280; font-weight: 400;">(Opsional)</span></span>
            </label>
            <div style="border: 2px dashed #cbd5e0; border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.3s;" 
                 id="upload-area"
                 onclick="document.getElementById('bukti_file').click();">
                <input type="file" id="bukti_file" name="bukti_file" accept=".pdf,.jpg,.jpeg,.png" 
                       style="display: none;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">📄</div>
                <p style="color: #4a5568; font-weight: 500; margin: 0;">Klik atau drag file ke sini</p>
                <p style="color: #718096; font-size: 0.9rem; margin: 0.5rem 0 0 0;">Format: PDF, JPG, PNG | Maks: 5 MB</p>
            </div>
            <p id="file-name" style="color: #059669; font-size: 0.9rem; margin-top: 0.5rem; display: none;"></p>
            @error('bukti_file')<p style="color: #dc2626; font-size: 0.85rem; margin-top: 0.5rem;">{{ $message }}</p>@enderror
        </div>

        <!-- Tombol Submit -->
        <div style="display: flex; justify-content: center; margin-top: 2rem;">
            <button type="submit" id="btn-submit" class="btn btn-primary" style="min-width: 200px;">
                <i class="fas fa-paper-plane"></i> Ajukan Izin
            </button>
        </div>
    </form>
</div>

<style>
    .btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%);
        color: white;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(3, 105, 161, 0.4);
    }
    .btn-primary:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .btn-primary:disabled:hover {
        transform: none;
        box-shadow: none;
    }
    .btn-secondary {
        background: #e2e8f0;
        color: #4a5568;
    }
    .btn-secondary:hover {
        background: #cbd5e0;
    }
    #upload-area:hover {
        background-color: #f0f9ff;
        border-color: #0369a1;
    }
    #upload-area.dragover {
        background-color: #e0f2fe;
        border-color: #0284c7;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSakit = document.getElementById('tipe-sakit');
        const tipeIzin = document.getElementById('tipe-izin');
        const alasanContainer = document.getElementById('alasan-container');
        const alasanInput = document.getElementById('alasan');
        const buktiLabel = document.getElementById('bukti-label');
        const buktiInput = document.getElementById('bukti_file');
        const uploadArea = document.getElementById('upload-area');
        const fileName = document.getElementById('file-name');
        const tanggalInput = document.getElementById('tanggal');
        const hariInput = document.getElementById('hari');
        const hariInfo = document.getElementById('hari-info');
        const hariText = document.getElementById('hari-text');
        const jadwalContainer = document.getElementById('jadwal-container');
        const jadwalSelect = document.getElementById('id_jadwal');
        const noJadwalWarning = document.getElementById('no-jadwal-warning');
        const noJadwalText = document.getElementById('no-jadwal-text');
        const btnSubmit = document.getElementById('btn-submit');

        // Get siswa kelas from auth
        const idKelas = {{ auth()->user()->siswa->id_kelas ?? 'null' }};

        // Nama hari dalam Bahasa Indonesia
        const namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        // Variable untuk track current request
        let currentFetchController = null;

        // Auto-detect hari from tanggal
        tanggalInput.addEventListener('change', function() {
            const tanggal = this.value;
            
            // Cancel previous fetch if exists
            if (currentFetchController) {
                currentFetchController.abort();
            }
            
            if (!tanggal) {
                hariInput.value = '';
                hariInfo.style.display = 'none';
                jadwalContainer.style.display = 'none';
                jadwalSelect.innerHTML = '<option value="">-- Pilih Jam Pelajaran --</option>';
                jadwalSelect.removeAttribute('required');
                return;
            }

            // Detect hari dari tanggal dengan cara yang PASTI
            const parts = tanggal.split('-');
            const year = parseInt(parts[0]);
            const month = parseInt(parts[1]) - 1;
            const day = parseInt(parts[2]);
            
            const date = new Date(year, month, day, 12, 0, 0); // Noon time
            const dayIndex = date.getDay();
            const hari = namaHari[dayIndex];
            
            console.log('=== DEBUG ===');
            console.log('Input tanggal:', tanggal);
            console.log('Parsed date:', date.toDateString());
            console.log('Day index:', dayIndex);
            console.log('Hari terdeteksi:', hari);
            
            // Update hidden input dan info
            hariInput.value = hari;
            hariText.textContent = hari;
            hariInfo.style.display = 'block';
            
            // Load jadwal untuk hari tersebut
            loadJadwal(hari);
        });

        // Function to load jadwal
        function loadJadwal(hari) {
            if (!hari) {
                jadwalContainer.style.display = 'none';
                jadwalSelect.innerHTML = '<option value="">-- Pilih Jam Pelajaran --</option>';
                jadwalSelect.removeAttribute('required');
                return;
            }

            if (!idKelas) {
                Swal.fire({
                    title: 'Error',
                    text: 'Data kelas tidak ditemukan',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
                return;
            }
            
            console.log('Loading jadwal untuk hari:', hari);
            
            // Create new AbortController for this request
            currentFetchController = new AbortController();

            // Fetch jadwal
            fetch(`/api/jadwal?id_kelas=${idKelas}&hari=${hari}`, {
                signal: currentFetchController.signal
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    
                    // Check if data is array or has error property
                    if (data.error) {
                        throw new Error(data.message || 'Gagal memuat jadwal');
                    }
                    
                    jadwalSelect.innerHTML = '<option value="">-- Pilih Jam Pelajaran --</option>';
                    
                    if (!Array.isArray(data) || data.length === 0) {
                        console.log('Tidak ada jadwal untuk hari:', hari);
                        // Show warning
                        noJadwalText.textContent = `Tidak ada jadwal pelajaran di hari ${hari}`;
                        noJadwalWarning.style.display = 'block';
                        jadwalContainer.style.display = 'none';
                        jadwalSelect.removeAttribute('required');
                        
                        // Disable submit button dan upload area
                        btnSubmit.disabled = true;
                        uploadArea.style.opacity = '0.5';
                        uploadArea.style.cursor = 'not-allowed';
                        uploadArea.onclick = null;
                        buktiInput.disabled = true;
                    } else {
                        console.log(`Ditemukan ${data.length} jadwal`);
                        // Hide warning
                        noJadwalWarning.style.display = 'none';
                        
                        // Enable submit button dan upload area
                        btnSubmit.disabled = false;
                        uploadArea.style.opacity = '1';
                        uploadArea.style.cursor = 'pointer';
                        uploadArea.onclick = function() { buktiInput.click(); };
                        buktiInput.disabled = false;
                        
                        data.forEach(jadwal => {
                            const option = document.createElement('option');
                            option.value = jadwal.id_jadwal;
                            option.textContent = `${jadwal.jam_mulai} - ${jadwal.jam_selesai} | ${jadwal.mata_pelajaran} (${jadwal.guru_nama})`;
                            jadwalSelect.appendChild(option);
                        });
                        jadwalContainer.style.display = 'block';
                        jadwalSelect.setAttribute('required', 'required');
                    }
                })
                .catch(error => {
                    if (error.name === 'AbortError') {
                        console.log('Fetch aborted');
                        return;
                    }
                    console.error('Error:', error);
                    // Hanya show alert kalau memang error server, bukan "tidak ada jadwal"
                    if (error.message !== 'Gagal memuat jadwal') {
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat memuat jadwal',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                    jadwalContainer.style.display = 'none';
                    jadwalSelect.removeAttribute('required');
                });
        }

        // Toggle alasan field based on tipe
        tipeSakit.addEventListener('change', function() {
            if (this.checked) {
                alasanContainer.style.display = 'none';
                alasanInput.removeAttribute('required');
                alasanInput.value = '';
                buktiLabel.innerHTML = 'Upload Surat Sakit <span style="color: #6b7280; font-weight: 400;">(Opsional)</span>';
            }
        });

        tipeIzin.addEventListener('change', function() {
            if (this.checked) {
                alasanContainer.style.display = 'block';
                alasanInput.setAttribute('required', 'required');
                buktiLabel.innerHTML = 'Upload Bukti Izin <span style="color: #6b7280; font-weight: 400;">(Opsional)</span>';
            }
        });

        // File upload handler
        buktiInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                
                if (file.size > 5 * 1024 * 1024) {
                    fileName.textContent = '❌ File terlalu besar (maks 5 MB)';
                    fileName.style.color = '#dc2626';
                    buktiInput.value = '';
                } else {
                    fileName.textContent = `✓ ${file.name} (${sizeMB} MB)`;
                    fileName.style.color = '#059669';
                    fileName.style.display = 'block';
                }
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function() {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                buktiInput.files = files;
                const event = new Event('change', { bubbles: true });
                buktiInput.dispatchEvent(event);
            }
        });

        // Form validation
        document.getElementById('form-ajukan-izin').addEventListener('submit', function(e) {
            const tipe = document.querySelector('input[name="tipe"]:checked');
            if (!tipe) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Pilih tipe ketidakhadiran (Sakit atau Izin)',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return false;
            }

            if (!hariInput.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Pilih tanggal terlebih dahulu',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return false;
            }

            if (!jadwalSelect.value) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Pilih jam pelajaran terlebih dahulu',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return false;
            }

            // Validasi alasan hanya untuk tipe IZIN
            if (tipe.value === 'izin') {
                const alasan = alasanInput.value.trim();
                if (!alasan || alasan.length < 10) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validasi Gagal',
                        text: 'Alasan izin wajib diisi minimal 10 karakter',
                        icon: 'warning',
                        confirmButtonColor: '#0369a1'
                    });
                    return false;
                }
            }
            
            // DEBUG console log
            console.log('=== FORM SUBMIT - VALIDASI PASSED ===');
            console.log('Tipe:', tipe.value);
            console.log('Tanggal:', tanggalInput.value);
            console.log('Hari:', hariInput.value);
            console.log('Jadwal:', jadwalSelect.value);
            console.log('Alasan:', alasanInput.value);
            
            // Show loading - JANGAN PREVENT DEFAULT, BIARKAN FORM SUBMIT!
            Swal.fire({
                title: 'Mengirim Izin...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // PENTING: Return true atau jangan prevent default agar form benar-benar submit!
            return true;
        });
    });
</script>

@endsection