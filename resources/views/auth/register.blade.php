<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - SMK YAPIM BIRU-BIRU</title>
    <link rel="icon" type="image/png" href="{{ asset('images/yapim.png?v=' . time() . '\)') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            max-width: 450px;
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        .form-group input::placeholder, .form-group select::placeholder {
            color: #999;
        }
        .form-group input:focus, .form-group select:focus {
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
            <h1>Register</h1>
            <p>Daftar Akun Baru</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required onchange="toggleKelasField()">
                    <option value="">Pilih Role</option>
                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                </select>
            </div>

            <div class="form-group" id="nisn-field" style="display: none;">
                <label for="nisn">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="Contoh: 0123456789">
            </div>

            <div class="form-group" id="kelas-field" style="display: none;">
                <label for="id_kelas">Kelas</label>
                <select id="id_kelas" name="id_kelas">
                    <option value="">Pilih Kelas</option>
                    @if(isset($kelas) && $kelas->count() > 0)
                        @php
                            $currentTingkat = null;
                        @endphp
                        @foreach($kelas as $item)
                            @if($currentTingkat !== $item->tingkat)
                                @if($currentTingkat !== null)
                                    </optgroup>
                                @endif
                                <optgroup label="Kelas {{ $item->tingkat }}">
                                @php
                                    $currentTingkat = $item->tingkat;
                                @endphp
                            @endif
                            <option value="{{ $item->id_kelas }}" {{ old('id_kelas') == $item->id_kelas ? 'selected' : '' }}>
                                {{ $item->nama_kelas }} - {{ $item->jurusan }}
                            </option>
                        @endforeach
                        @if($currentTingkat !== null)
                            </optgroup>
                        @endif
                    @else
                        <option value="">Tidak ada kelas tersedia</option>
                    @endif
                </select>
            </div>

            <div class="form-group" id="nip-field" style="display: none;">
                <label for="nip">NIP (Nomor Induk Pegawai)</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 123456789012345678">
            </div>

            <div class="form-group has-password">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group has-password">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password">
                    <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>

        <div class="login-link">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
        </div>
    </div>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += '{{ $error }}<br>';
            @endforeach
            
            Swal.fire({
                title: 'Terjadi Kesalahan!',
                html: errorMessages,
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
        // Toggle field kelas, NISN, dan NIP berdasarkan role yang dipilih
        function toggleKelasField() {
            const role = document.getElementById('role').value;
            const kelasField = document.getElementById('kelas-field');
            const nisnField = document.getElementById('nisn-field');
            const nipField = document.getElementById('nip-field');
            const kelasSelect = document.getElementById('id_kelas');
            const nisnInput = document.getElementById('nisn');
            const nipInput = document.getElementById('nip');
            
            if (role === 'siswa') {
                kelasField.style.display = 'block';
                nisnField.style.display = 'block';
                nipField.style.display = 'none';
                
                kelasSelect.setAttribute('required', 'required');
                nisnInput.setAttribute('required', 'required');
                nipInput.removeAttribute('required');
                nipInput.value = ''; // Reset NIP
            } else if (role === 'guru') {
                kelasField.style.display = 'none';
                nisnField.style.display = 'none';
                nipField.style.display = 'block';
                
                kelasSelect.removeAttribute('required');
                nisnInput.removeAttribute('required');
                nipInput.setAttribute('required', 'required');
                kelasSelect.value = ''; // Reset pilihan kelas
                nisnInput.value = ''; // Reset NISN
            } else {
                kelasField.style.display = 'none';
                nisnField.style.display = 'none';
                nipField.style.display = 'none';
                
                kelasSelect.removeAttribute('required');
                nisnInput.removeAttribute('required');
                nipInput.removeAttribute('required');
                kelasSelect.value = '';
                nisnInput.value = '';
                nipInput.value = '';
            }
        }

        // Jalankan saat halaman dimuat untuk handle old() value
        document.addEventListener('DOMContentLoaded', function() {
            toggleKelasField();
        });

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
    </script>
</body>
</html>

