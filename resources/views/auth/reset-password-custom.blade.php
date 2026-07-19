<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - SMK YAPIM BIRU-BIRU</title>
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
        .reset-container {
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
        .reset-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .reset-header h1 {
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .reset-header p {
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
        .input-group {
            position: relative;
        }
        .input-group input {
            width: 100%;
            padding: 0.85rem 2.5rem 0.85rem 0.85rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        .input-group input::placeholder {
            color: #999;
        }
        .input-group input:focus {
            outline: none;
            border-color: #0369a1;
            box-shadow: 0 0 0 3px rgba(3, 105, 161, 0.1);
        }
        .toggle-password {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 1rem;
            transition: color 0.3s;
            padding: 0.5rem;
        }
        .toggle-password:hover {
            color: #0369a1;
        }
        .password-strength {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.3rem;
            align-items: center;
        }
        .strength-bar {
            flex: 1;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            background: #ef4444;
        }
        .strength-text {
            font-size: 0.85rem;
            color: #666;
            min-width: 80px;
        }
        .strength-text.weak {
            color: #ef4444;
            font-weight: 600;
        }
        .strength-text.fair {
            color: #f97316;
            font-weight: 600;
        }
        .strength-text.good {
            color: #eab308;
            font-weight: 600;
        }
        .strength-text.strong {
            color: #22c55e;
            font-weight: 600;
        }
        .requirement-list {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .requirement-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: #666;
        }
        .requirement-item:last-child {
            margin-bottom: 0;
        }
        .requirement-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            background: #e0e0e0;
            color: #666;
        }
        .requirement-icon.met {
            background: #22c55e;
            color: white;
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
            margin-top: 1.5rem;
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
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="logo-container">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo MSBD">
        </div>
        <div class="reset-header">
            <h1>Reset Password</h1>
            <p>Buat password baru yang kuat untuk akun Anda</p>
        </div>

        <form id="resetForm" onsubmit="handleResetPassword(event)">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" required 
                           placeholder="Masukkan password baru"
                           onchange="updatePasswordStrength()"
                           oninput="updatePasswordStrength()">
                    <button type="button" class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-text" id="strengthText">Masukkan password</span>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="input-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" required 
                           placeholder="Konfirmasi password"
                           oninput="checkPasswordMatch()">
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="requirement-list">
                <div class="requirement-item">
                    <div class="requirement-icon" id="req-length">✓</div>
                    <span>Minimal 8 karakter</span>
                </div>
                <div class="requirement-item">
                    <div class="requirement-icon" id="req-upper">✓</div>
                    <span>Minimal 1 huruf besar (A-Z)</span>
                </div>
                <div class="requirement-item">
                    <div class="requirement-icon" id="req-lower">✓</div>
                    <span>Minimal 1 huruf kecil (a-z)</span>
                </div>
                <div class="requirement-item">
                    <div class="requirement-icon" id="req-number">✓</div>
                    <span>Minimal 1 angka (0-9)</span>
                </div>
                <div class="requirement-item">
                    <div class="requirement-icon" id="req-match">✓</div>
                    <span>Password cocok dengan konfirmasi</span>
                </div>
            </div>

            <button type="submit" class="btn" id="submitBtn">
                <span id="btnText">Reset Password</span>
                <div class="loading-spinner" id="spinner"></div>
            </button>
        </form>

        <div class="login-link">
            <p><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Kembali ke Login</a></p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = event.currentTarget;
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function updatePasswordStrength() {
            const password = document.getElementById('password').value;
            let strength = 0;

            // Check requirements
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);

            // Update requirement indicators
            updateRequirement('req-length', hasLength);
            updateRequirement('req-upper', hasUpper);
            updateRequirement('req-lower', hasLower);
            updateRequirement('req-number', hasNumber);

            // Calculate strength
            if (hasLength) strength++;
            if (hasUpper) strength++;
            if (hasLower) strength++;
            if (hasNumber) strength++;

            // Update strength display
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            const fillPercentage = (strength / 4) * 100;
            strengthFill.style.width = fillPercentage + '%';

            let label = 'Lemah';
            let color = '#ef4444';
            let className = 'weak';

            if (strength === 0) {
                label = 'Masukkan password';
                className = '';
            } else if (strength === 1) {
                label = 'Lemah';
                className = 'weak';
            } else if (strength === 2) {
                label = 'Cukup';
                color = '#f97316';
                className = 'fair';
            } else if (strength === 3) {
                label = 'Baik';
                color = '#eab308';
                className = 'good';
            } else if (strength === 4) {
                label = 'Kuat';
                color = '#22c55e';
                className = 'strong';
            }

            strengthText.textContent = label;
            strengthText.className = 'strength-text ' + className;
            strengthFill.style.background = color;

            checkPasswordMatch();
        }

        function updateRequirement(id, met) {
            const element = document.getElementById(id);
            if (met) {
                element.classList.add('met');
                element.innerHTML = '<i class="fas fa-check"></i>';
            } else {
                element.classList.remove('met');
                element.innerHTML = '✓';
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const match = password && confirm && password === confirm;

            updateRequirement('req-match', match);
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;

            // All requirements
            const hasLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const match = password === confirm;

            return hasLength && hasUpper && hasLower && hasNumber && match;
        }

        function handleResetPassword(event) {
            event.preventDefault();

            if (!validatePassword()) {
                Swal.fire({
                    title: 'Validasi!',
                    text: 'Password tidak memenuhi semua kriteria',
                    icon: 'warning',
                    confirmButtonColor: '#0369a1'
                });
                return;
            }

            const password = document.getElementById('password').value;
            const token = document.querySelector('input[name="token"]').value;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');

            // Show loading
            submitBtn.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Memproses...';

            const passwordConfirm = document.getElementById('password_confirmation').value;

            fetch('{{ route("reset-password.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    token: token,
                    password: password,
                    password_confirmation: passwordConfirm
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Password telah direset. Silakan login dengan password baru.',
                        icon: 'success',
                        confirmButtonColor: '#0369a1',
                        confirmButtonText: 'Login Sekarang'
                    }).then(() => {
                        window.location.href = '{{ route("login") }}';
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
                btnText.textContent = 'Reset Password';
            });
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            updatePasswordStrength();
        });
    </script>
</body>
</html>

