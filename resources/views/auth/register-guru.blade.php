<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Guru - SMK YAPIM BIRU-BIRU</title>
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
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="register-header">
            <h1>Registrasi Guru</h1>
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

        <form action="{{ route('register.guru.submit') }}" method="POST">
            @csrf

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="name">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                <input type="text" id="name" name="name" required 
                       placeholder="Masukkan nama lengkap"
                       value="{{ old('name') }}">
                @error('name')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email <span style="color: #dc2626;">*</span></label>
                <input type="email" id="email" name="email" required 
                       placeholder="contoh@email.com"
                       value="{{ old('email') }}">
                <p class="hint-text">Email digunakan untuk login</p>
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Nomor HP -->
            <div class="form-group">
                <label for="no_hp">Nomor HP <span style="color: #dc2626;">*</span></label>
                <input type="text" id="no_hp" name="no_hp" required 
                       placeholder="08xxxxxxxxxx"
                       value="{{ old('no_hp') }}"
                       pattern="[0-9]{10,15}"
                       title="Nomor HP harus berisi 10-15 digit angka">
                @error('no_hp')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Jenis Kelamin -->
            <div class="form-row">
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin <span style="color: #dc2626;">*</span></label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<p class="error-text">{{ $message }}</p>@enderror
                </div>

                <!-- Agama -->
                <div class="form-group">
                    <label for="agama">Agama <span style="color: #dc2626;">*</span></label>
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

            <!-- Password -->
            <div class="form-group has-password">
                <label for="password">Password <span style="color: #dc2626;">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required 
                           placeholder="Minimal 6 karakter">
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group has-password">
                <label for="password_confirmation">Konfirmasi Password <span style="color: #dc2626;">*</span></label>
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
            <p style="margin-top: 0.5rem;"><a href="{{ route('register.siswa') }}">Daftar sebagai Siswa</a></p>
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

