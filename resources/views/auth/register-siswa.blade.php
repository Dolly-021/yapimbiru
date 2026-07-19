<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Siswa - SMK YAPIM BIRU-BIRU</title>
    <link rel="icon" type="image/png" href="{{ asset('images/yapim.png?v=' . time() . '\)') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .register-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 600px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-header h1 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .register-header p {
            color: #666;
            font-size: 0.95rem;
            font-weight: 400;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        .form-group input::placeholder, .form-group select::placeholder, .form-group textarea::placeholder {
            color: #999;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #0369a1;
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
        }
        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.5rem;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        .btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(3, 105, 161, 0.3);
        }
        .btn:active {
            transform: translateY(0);
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        .login-link a {
            color: #0369a1;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .login-link a:hover {
            color: #06b6d4;
            text-decoration: underline;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.95rem;
        }
        .alert-error {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
        }
        .alert-success {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
            color: #15803d;
        }
        .alert-info {
            background: #e0f2fe;
            border-left: 4px solid #0284c7;
            color: #0c4a6e;
        }
        .hint-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        .error-text {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .password-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 1.1rem;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover {
            color: #0369a1;
        }
        .form-group.has-password input {
            padding-right: 2.8rem;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="register-header">
            <h1>Registrasi Siswa</h1>
            <p>Sistem Informasi Manajemen Akademik SMK</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <form action="{{ route('register.siswa.submit') }}" method="POST">
            @csrf

            <!-- NIS -->
            <div class="form-group">
                <label for="nis">NIS (Nomor Induk Siswa)</label>
                <input type="text" id="nis" name="nis" required 
                       placeholder="Masukkan NIS (minimal 10 digit)"
                       value="{{ old('nis') }}">
                <p class="hint-text">Ketik 10 digit NIS untuk mengecek data siswa</p>
                @error('nis')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required
                       placeholder="Nama akan otomatis terisi jika NIS ditemukan"
                       value="{{ old('name') }}">
                <p class="hint-text" id="name-hint">Jika NIS tidak ditemukan, wajib diisi manual</p>
                @error('name')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       placeholder="contoh@email.com"
                       value="{{ old('email') }}">
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Nomor HP -->
            <div class="form-group">
                <label for="no_hp">Nomor HP</label>
                <input type="text" id="no_hp" name="no_hp" required 
                       placeholder="08xxxxxxxxxx"
                       value="{{ old('no_hp') }}"
                       pattern="[0-9]{10,15}"
                       title="Nomor HP harus berisi 10-15 digit angka">
                @error('no_hp')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Tempat Lahir -->
            <div class="form-row">
                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" id="tempat_lahir" name="tempat_lahir" required 
                           placeholder="Kota/Kabupaten"
                           value="{{ old('tempat_lahir') }}">
                    @error('tempat_lahir')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <!-- Tanggal Lahir -->
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" required 
                           value="{{ old('tanggal_lahir') }}">
                    @error('tanggal_lahir')<p class="error-text">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-row">
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <!-- Agama -->
                <div class="form-group">
                    <label for="agama">Agama</label>
                    <select id="agama" name="agama" required>
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katholik" {{ old('agama') == 'Katholik' ? 'selected' : '' }}>Katholik</option>
                        <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}>Budha</option>
                        <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                    @error('agama')<p class="error-text">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Kelas -->
            <div class="form-group">
                <label for="id_kelas">Kelas</label>
                <select id="id_kelas" name="id_kelas" required>
                    <option value="">Pilih Kelas</option>
                    @if(isset($kelas) && $kelas->count() > 0)
                        @php $currentTingkat = null; @endphp
                        @foreach($kelas as $item)
                            @if($currentTingkat !== $item->tingkat)
                                @if($currentTingkat !== null)</optgroup>@endif
                                <optgroup label="Kelas {{ $item->tingkat }}">
                                @php $currentTingkat = $item->tingkat; @endphp
                            @endif
                            <option value="{{ $item->id_kelas }}" {{ old('id_kelas') == $item->id_kelas ? 'selected' : '' }}>
                                {{ $item->nama_kelas }} - {{ $item->jurusan }}
                            </option>
                        @endforeach
                        @if($currentTingkat !== null)</optgroup>@endif
                    @else
                        <option value="">Tidak ada kelas tersedia</option>
                    @endif
                </select>
                @error('id_kelas')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Semester (Hidden field - auto-filled) -->
            <input type="hidden" id="semester" name="semester" value="">
            <div class="form-group">
                <label>Semester</label>
                <div style="padding: 0.85rem; border: 2px solid #e0e0e0; border-radius: 8px; background: #f9fafb; color: #666; font-size: 0.95rem;">
                    <span id="semester-display">Akan terisi otomatis sesuai kelas</span>
                </div>
                <p class="hint-text">Semester akan otomatis terisi berdasarkan data master siswa</p>
                @error('semester')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Sekolah Asal -->
            <div class="form-group">
                <label for="sekolah_asal">Sekolah Asal</label>
                <input type="text" id="sekolah_asal" name="sekolah_asal" required 
                       placeholder="Nama sekolah asal (SMP/Setara)"
                       value="{{ old('sekolah_asal') }}">
                <p class="hint-text">Akan otomatis terisi jika NIS ditemukan di database</p>
                @error('sekolah_asal')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Alamat -->
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" required 
                          placeholder="Jalan, nomor, RT/RW, Kota, Provinsi"
                          maxlength="500">{{ old('alamat') }}</textarea>
                <p class="hint-text">Akan otomatis terisi jika NIS ditemukan di database</p>
                @error('alamat')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Password -->
            <div class="form-group has-password">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required 
                           placeholder="Minimal 8 karakter">
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group has-password">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           placeholder="Ulangi password">
                    <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password_confirmation')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn">Daftar Sekarang</button>
        </form>

        <div class="login-link">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
            <p style="margin-top: 0.5rem;"><a href="{{ route('register.guru') }}">Daftar sebagai Guru</a></p>
        </div>
    </div>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach
            
            Swal.fire({
                title: 'Terjadi Kesalahan!',
                text: errorMessages,
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#0369a1'
            });
        });
    </script>
    @endif

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#0369a1',
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    <script>
        // Check NIS saat user selesai input
        const nisInput = document.getElementById('nis');
        const nameInput = document.getElementById('name');
        const nameHint = document.getElementById('name-hint');
        const tempatLahirInput = document.getElementById('tempat_lahir');
        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        const jenisKelaminSelect = document.getElementById('jenis_kelamin');
        const agamaSelect = document.getElementById('agama');
        const idKelasSelect = document.getElementById('id_kelas');
        const noHpInput = document.getElementById('no_hp');
        const sekolahAsalInput = document.getElementById('sekolah_asal');
        const alamatInput = document.getElementById('alamat');

        // Event listener untuk unlock form ketika NIS diubah/dihapus
        nisInput.addEventListener('input', function() {
            if (this.value.length < 10) {
                // Unlock semua field ketika NIS dihapus/diubah
                nameInput.readOnly = false;
                tempatLahirInput.readOnly = false;
                tanggalLahirInput.readOnly = false;
                jenisKelaminSelect.disabled = false;
                jenisKelaminSelect.style.pointerEvents = 'auto';
                jenisKelaminSelect.style.opacity = '1';
                agamaSelect.disabled = false;
                agamaSelect.style.pointerEvents = 'auto';
                agamaSelect.style.opacity = '1';
                idKelasSelect.disabled = false;
                idKelasSelect.style.pointerEvents = 'auto';
                idKelasSelect.style.opacity = '1';
                noHpInput.readOnly = false;
                sekolahAsalInput.readOnly = false;
                alamatInput.readOnly = false;
                
                nameHint.innerHTML = '';
                nameHint.style.color = '#666';
            }
        });

        nisInput.addEventListener('blur', function() {
            if (this.value.length >= 10) {
                checkNIS(this.value);
            }
        });

        function checkNIS(nis) {
            fetch(`/api/check-nis/${nis}`)
                .then(response => response.json())
                .then(data => {
                    // Prioritas 1: Cek apakah NIS sudah terdaftar (punya akun)
                    if (data.already_registered) {
                        // Kunci semua form
                        nameInput.value = '';
                        tempatLahirInput.value = '';
                        tanggalLahirInput.value = '';
                        noHpInput.value = '';
                        sekolahAsalInput.value = '';
                        alamatInput.value = '';
                        jenisKelaminSelect.value = '';
                        agamaSelect.value = '';
                        idKelasSelect.value = '';
                        
                        nameInput.readOnly = true;
                        tempatLahirInput.readOnly = true;
                        tanggalLahirInput.readOnly = true;
                        jenisKelaminSelect.disabled = true;
                        agamaSelect.disabled = true;
                        idKelasSelect.disabled = true;
                        noHpInput.readOnly = true;
                        sekolahAsalInput.readOnly = true;
                        alamatInput.readOnly = true;
                        
                        nameHint.innerHTML = '<i class="fas fa-exclamation-circle"></i> NIS sudah terdaftar';
                        nameHint.style.color = '#dc2626';
                        
                        Swal.fire({
                            title: 'NIS Sudah Terdaftar!',
                            text: 'NIS ini sudah pernah terdaftar. Silakan login dengan akun Anda atau hubungi admin jika ada masalah.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626'
                        });
                        
                        return; // Stop eksekusi
                    }
                    
                    // Prioritas 2: Jika ditemukan di data master dan belum terdaftar
                    if (data.found) {
                        const siswaData = data.data;
                        
                        // Auto-fill semua field dengan data dari database
                        nameInput.value = siswaData.nama_siswa || '';
                        tempatLahirInput.value = siswaData.tempat_lahir || '';
                        tanggalLahirInput.value = siswaData.tanggal_lahir || '';
                        noHpInput.value = siswaData.no_hp || '';
                        sekolahAsalInput.value = siswaData.sekolah_asal || '';
                        alamatInput.value = siswaData.alamat || '';
                        
                        // Set dropdown untuk jenis kelamin
                        if (siswaData.jenis_kelamin) {
                            jenisKelaminSelect.value = siswaData.jenis_kelamin;
                        }
                        
                        // Set dropdown untuk agama
                        if (siswaData.agama) {
                            agamaSelect.value = siswaData.agama;
                        }
                        
                        // Set dropdown untuk kelas
                        if (siswaData.id_kelas) {
                            idKelasSelect.value = siswaData.id_kelas;
                            // Trigger change event untuk auto-fill semester
                            idKelasSelect.dispatchEvent(new Event('change'));
                        }
                        
                        // Set all fields sebagai readOnly untuk mencegah perubahan data
                        nameInput.readOnly = true;
                        tempatLahirInput.readOnly = true;
                        tanggalLahirInput.readOnly = true;
                        
                        // Untuk select, jangan pakai disabled agar value tetap terkirim
                        // Pakai style pointer-events untuk mencegah user mengubah
                        jenisKelaminSelect.style.pointerEvents = 'none';
                        jenisKelaminSelect.style.opacity = '0.6';
                        agamaSelect.style.pointerEvents = 'none';
                        agamaSelect.style.opacity = '0.6';
                        idKelasSelect.style.pointerEvents = 'none';
                        idKelasSelect.style.opacity = '0.6';
                        
                        noHpInput.readOnly = false; // Biarkan user edit nomor HP
                        sekolahAsalInput.readOnly = true;
                        alamatInput.readOnly = true;
                        
                        nameHint.innerHTML = '<i class="fas fa-check-circle"></i> Data siswa ditemukan - Field otomatis terisi';
                        nameHint.style.color = '#059669';
                    } else {
                        // NIS tidak ditemukan di master data, buka form untuk isi manual
                        nameInput.readOnly = false;
                        tempatLahirInput.readOnly = false;
                        tanggalLahirInput.readOnly = false;
                        jenisKelaminSelect.disabled = false;
                        jenisKelaminSelect.style.pointerEvents = 'auto';
                        jenisKelaminSelect.style.opacity = '1';
                        agamaSelect.disabled = false;
                        agamaSelect.style.pointerEvents = 'auto';
                        agamaSelect.style.opacity = '1';
                        idKelasSelect.disabled = false;
                        idKelasSelect.style.pointerEvents = 'auto';
                        idKelasSelect.style.opacity = '1';
                        noHpInput.readOnly = false;
                        sekolahAsalInput.readOnly = false;
                        alamatInput.readOnly = false;
                        
                        nameInput.value = '';
                        tempatLahirInput.value = '';
                        tanggalLahirInput.value = '';
                        noHpInput.value = '';
                        sekolahAsalInput.value = '';
                        alamatInput.value = '';
                        jenisKelaminSelect.value = '';
                        agamaSelect.value = '';
                        idKelasSelect.value = '';
                        
                        nameHint.innerHTML = '<i class="fas fa-info-circle"></i> NIS tidak ditemukan, silakan isi data manual';
                        nameHint.style.color = '#666';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Error network atau server, reset form agar bisa diisi manual
                    nameInput.readOnly = false;
                    tempatLahirInput.readOnly = false;
                    tanggalLahirInput.readOnly = false;
                    jenisKelaminSelect.disabled = false;
                    agamaSelect.disabled = false;
                    idKelasSelect.disabled = false;
                    noHpInput.readOnly = false;
                    sekolahAsalInput.readOnly = false;
                    alamatInput.readOnly = false;
                    
                    nameHint.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error saat mengecek NIS. Silakan isi data manual.';
                    nameHint.style.color = '#dc2626';
                });
        }

        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggle = event.target.closest('.password-toggle');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordField.type = 'password';
                toggle.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

        // Function untuk auto-fill semester berdasarkan kelas
        function autoFillSemester() {
            const kelasSelect = document.getElementById('id_kelas');
            const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
            const semesterInput = document.getElementById('semester');
            const semesterDisplay = document.getElementById('semester-display');
            
            if (!selectedOption.value) {
                semesterInput.value = '';
                semesterDisplay.textContent = 'Akan terisi otomatis sesuai kelas';
                return;
            }
            
            const selectedText = selectedOption.text;
            let semesterValue = '';
            
            // Deteksi tingkat dari teks kelas
            // Semua siswa yang registrasi di 2025/2026 mengikuti periode 2025/2026
            if (selectedText.includes('XII')) {
                semesterValue = 'XII Semester Ganjil 2025/2026';
                semesterDisplay.textContent = semesterValue;
            } else if (selectedText.includes('XI')) {
                semesterValue = 'XI Semester Ganjil 2025/2026';
                semesterDisplay.textContent = semesterValue;
            } else if (selectedText.includes('X')) {
                semesterValue = 'X Semester Ganjil 2025/2026';
                semesterDisplay.textContent = semesterValue;
            }
            
            semesterInput.value = semesterValue;
        }
        
        // Event listener saat kelas berubah
        document.getElementById('id_kelas').addEventListener('change', autoFillSemester);

        // Trigger event saat halaman load jika ada kelas yang sudah dipilih
        window.addEventListener('DOMContentLoaded', function() {
            autoFillSemester();
        });
    </script>
</body>
</html>

