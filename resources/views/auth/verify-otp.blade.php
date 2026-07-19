<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi OTP - SMK YAPIM BIRU-BIRU</title>
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
        .verify-container {
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
        .verify-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .verify-header h1 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .verify-header p {
            color: #666;
            font-size: 0.95rem;
            font-weight: 400;
        }
        .phone-info {
            background: #f0f9ff;
            border: 1px solid #7dd3fc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #0369a1;
            font-size: 0.95rem;
        }
        .phone-info strong {
            display: block;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }
        .timer {
            text-align: center;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.95rem;
        }
        .timer .time {
            display: inline-block;
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            color: #856404;
            font-weight: 600;
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
        .otp-input {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .otp-input input {
            width: 100%;
            height: 3rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
            font-family: monospace;
        }
        .otp-input input:focus {
            outline: none;
            border-color: #0369a1;
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
        }
        .otp-input input::placeholder {
            color: #ccc;
        }
        .form-group textarea {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: monospace;
            resize: none;
            height: 60px;
        }
        .form-group textarea:focus {
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
            margin-top: 0.5rem;
        }
        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(3, 105, 161, 0.3);
        }
        .btn:active:not(:disabled) {
            transform: translateY(0);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            margin-top: 0.5rem;
        }
        .btn-secondary:hover:not(:disabled) {
            box-shadow: 0 10px 25px rgba(100, 116, 139, 0.3);
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
        .loading-spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #0369a1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .info-box {
            background: #f5f5f5;
            border-left: 4px solid #0369a1;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            color: #333;
        }
        .info-box i {
            margin-right: 0.5rem;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="verify-header">
            <h1>Verifikasi OTP</h1>
            <p>Masukkan kode OTP yang dikirim ke WhatsApp</p>
        </div>

        <div class="phone-info">
            <i class="fas fa-mobile-alt"></i>
            Kode dikirim ke
            <strong>{{ $phoneNumber ?? '**' }}</strong>
        </div>

        <div class="timer">
            Kode OTP berlaku selama:
            <div class="time">
                <i class="fas fa-hourglass-end"></i>
                <span id="timerDisplay">10:00</span>
            </div>
        </div>

        <form id="verifyForm" onsubmit="handleVerifyOtp(event)">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Masukkan 6 digit OTP atau paste seluruh kode di bawah
            </div>

            <div class="form-group">
                <label>Kode OTP (6 Digit)</label>
                <div class="otp-input" id="otpInputContainer">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                    <input type="text" class="otp-digit" maxlength="1" pattern="[0-9]" inputmode="numeric" placeholder="0">
                </div>
            </div>

            <div class="form-group">
                <label>Atau Paste Kode Lengkap</label>
                <textarea id="otpPaste" placeholder="Paste OTP 6 digit di sini"></textarea>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                <span id="btnText">Verifikasi OTP</span>
                <div class="loading-spinner" id="spinner"></div>
            </button>
        </form>

        <div class="login-link">
            <p>
                <a href="{{ route('forgot-password') }}">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </p>
        </div>
    </div>

    <script>
        // Timer functionality
        function startTimer() {
            let timeLeft = 10 * 60; // 10 minutes
            const display = document.getElementById('timerDisplay');

            const interval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                display.textContent = 
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');

                if (timeLeft <= 0) {
                    clearInterval(interval);
                    document.getElementById('submitBtn').disabled = true;
                    Swal.fire({
                        title: 'OTP Expired',
                        text: 'Waktu verifikasi OTP telah habis',
                        icon: 'error',
                        confirmButtonColor: '#0369a1'
                    }).then(() => {
                        window.location.href = '{{ route("forgot-password") }}';
                    });
                }
                timeLeft--;
            }, 1000);
        }

        // Auto-move between OTP inputs
        document.querySelectorAll('.otp-digit').forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value && index < 5) {
                    document.querySelectorAll('.otp-digit')[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    document.querySelectorAll('.otp-digit')[index - 1].focus();
                }
            });
        });

        // Handle paste functionality
        document.getElementById('otpPaste').addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pastedText.replace(/\D/g, '').slice(0, 6);

            if (digits.length === 6) {
                digits.split('').forEach((digit, index) => {
                    document.querySelectorAll('.otp-digit')[index].value = digit;
                });
                document.querySelectorAll('.otp-digit')[5].focus();
            }
        });

        function handleVerifyOtp(event) {
            event.preventDefault();

            // Get OTP from individual inputs
            let otp = Array.from(document.querySelectorAll('.otp-digit'))
                .map(input => input.value)
                .join('');

            // Or from paste textarea
            if (!otp || otp.length !== 6) {
                const pastedOtp = document.getElementById('otpPaste').value.replace(/\D/g, '');
                otp = pastedOtp.slice(0, 6);
            }

            if (!otp || otp.length !== 6) {
                Swal.fire({
                    title: 'Validasi!',
                    text: 'Silakan masukkan 6 digit OTP',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return;
            }

            const token = document.querySelector('input[name="token"]').value;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');

            // Show loading
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Memverifikasi...';

            fetch('{{ route("forgot-password.verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    token: token,
                    otp: otp
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'OTP terverifikasi. Silakan buat password baru.',
                        icon: 'success',
                        confirmButtonColor: '#0369a1',
                        confirmButtonText: 'Lanjutkan'
                    }).then(() => {
                        window.location.href = `/reset-password/${token}`;
                    });
                } else {
                    const message = data.message || 'OTP tidak valid';
                    const attemptsLeft = data.attempts_left;

                    let errorText = message;
                    if (attemptsLeft !== undefined && attemptsLeft > 0) {
                        errorText += `\n\nSisa percobaan: ${attemptsLeft}/5`;
                    }

                    Swal.fire({
                        title: 'Gagal!',
                        text: errorText,
                        icon: 'error',
                        confirmButtonColor: '#0369a1'
                    });

                    // Clear inputs
                    document.querySelectorAll('.otp-digit').forEach(input => input.value = '');
                    document.getElementById('otpPaste').value = '';
                    document.querySelectorAll('.otp-digit')[0].focus();
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
                btnText.textContent = 'Verifikasi OTP';
            });
        }

        // Start timer on page load
        document.addEventListener('DOMContentLoaded', () => {
            startTimer();
            document.querySelectorAll('.otp-digit')[0].focus();
        });
    </script>
</body>
</html>

