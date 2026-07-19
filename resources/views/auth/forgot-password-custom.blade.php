<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - SMK YAPIM BIRU-BIRU</title>
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
        .forgot-container {
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
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .forgot-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .forgot-header h1 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .forgot-header p {
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
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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
        .hint-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0369a1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="forgot-header">
            <h1>Lupa Password?</h1>
            <p>Masukkan identitas Anda untuk reset password</p>
        </div>

        <form id="forgotForm" onsubmit="handleForgotPassword(event)">
            @csrf

            <div class="form-group">
                <label for="identifier">NIS / NIP / Email</label>
                <input type="text" id="identifier" name="identifier" required 
                       placeholder="Masukkan NIS, NIP, atau Email Anda"
                       autocomplete="off">
                <p class="hint-text">
                    <i class="fas fa-info-circle"></i>
                    Gunakan NIS (siswa), NIP (guru), atau email yang terdaftar
                </p>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                <span id="btnText">Kirim OTP</span>
                <div class="loading-spinner" id="spinner"></div>
            </button>
        </form>

        <div class="login-link">
            <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke Login</a></p>
        </div>
    </div>

    <script>
        function handleForgotPassword(event) {
            event.preventDefault();
            
            const identifier = document.getElementById('identifier').value.trim();
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');

            if (!identifier) {
                Swal.fire({
                    title: 'Validasi!',
                    text: 'Silakan isi NIS/NIP/Email',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return;
            }

            // Show loading
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Mengirim...';

            fetch('{{ route("forgot-password.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    identifier: identifier
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: `
                            <p>OTP telah dikirim ke WhatsApp</p>
                            <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #666;">
                                <i class="fas fa-mobile-alt"></i> ${data.phone}
                            </p>
                        `,
                        icon: 'success',
                        confirmButtonColor: '#0369a1',
                        confirmButtonText: 'Lanjutkan'
                    }).then(() => {
                        // Redirect ke halaman verifikasi OTP
                        window.location.href = `/forgot-password/verify/${data.token}`;
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan',
                        icon: 'error',
                        confirmButtonColor: '#0369a1'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan',
                    icon: 'error',
                    confirmButtonColor: '#0369a1'
                });
            })
            .finally(() => {
                // Hide loading
                submitBtn.disabled = false;
                spinner.style.display = 'none';
                btnText.textContent = 'Kirim OTP';
            });
        }
    </script>
</body>
</html>

