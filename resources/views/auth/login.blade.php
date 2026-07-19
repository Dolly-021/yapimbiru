<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0369a1">
    <title>Login - SMK YAPIM BIRU-BIRU</title>
    <link rel="icon" type="image/png" href="{{ asset('images/yapim.png?v=' . time()) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png?v=' . time()) }}" type="image/png">
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
        .login-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 420px;
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
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .login-header p {
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
        .form-group input {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        .form-group input::placeholder {
            color: #999;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0369a1;
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
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
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .register-link a {
            color: #0369a1;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }
        .register-link a:hover {
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
        .register-options {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .register-options p {
            text-align: center;
            color: #666;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .register-buttons {
            display: flex;
            gap: 1rem;
        }
        .register-btn {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .register-btn-guru {
            background: white;
            color: #2563eb;
            border-color: #2563eb;
        }
        .register-btn-guru:hover {
            background: #2563eb;
            color: white;
            transform: translateY(-2px);
        }
        .register-btn-siswa {
            background: white;
            color: #0ea5e9;
            border-color: #0ea5e9;
        }
        .register-btn-siswa:hover {
            background: #0ea5e9;
            color: white;
            transform: translateY(-2px);
        }
        .icon {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="login-header">
            <h1>Login</h1>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="identifier">NIS / Username / Email</label>
                <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="Masukkan NIS, Username, atau Email" required>
                <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 0.25rem;">
                </small>
            </div>

            <div class="form-group has-password">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link" style="margin-top: 1rem; text-align: center;">
                <a href="{{ route('forgot-password') }}" style="font-size: 0.9rem; color: #666;">
                    <i class="fas fa-key"></i> Lupa Password?
                </a>
            </div>
        </form>

        <!-- Opsi Registrasi -->
        <div class="register-options">
            <p>Belum punya akun? Daftar sebagai:</p>
            <div class="register-buttons">
                <a href="{{ route('register.guru') }}" class="register-btn register-btn-guru">
                    <i class="fas fa-chalkboard-teacher icon"></i>
                    Guru
                </a>
                <a href="{{ route('register.siswa') }}" class="register-btn register-btn-siswa">
                    <i class="fas fa-user-graduate icon"></i>
                    Siswa
                </a>
            </div>
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
                confirmButtonColor: '#667eea'
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
                confirmButtonColor: '#667eea',
                timer: 3000,
                timerProgressBar: true
            });
        });
    </script>
    @endif

    @if(session('success_sweet'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "{{ session('success_sweet')['title'] }}",
                text: "{{ session('success_sweet')['message'] }}",
                icon: "{{ session('success_sweet')['icon'] }}",
                confirmButtonText: 'Oke',
                confirmButtonColor: '#667eea'
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Gagal Login!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#667eea'
            });
        });
    </script>
    @endif

    @if(session('error_sweet'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: "{{ session('error_sweet')['title'] }}",
                text: "{{ session('error_sweet')['message'] }}",
                icon: "{{ session('error_sweet')['icon'] }}",
                confirmButtonText: 'Oke',
                confirmButtonColor: '#667eea'
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
