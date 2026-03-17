<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: url('{{ asset('assets/img/BgForLoginAndRegister.png') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .reset-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4CAF50, #8BC34A);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-container ion-icon {
            font-size: 40px;
            color: white;
        }

        .header-section h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .header-section p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #4CAF50;
            font-size: 20px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 20px;
        }

        .form-control {
            width: 100%;
            padding: 14px 50px 14px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .password-requirements {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 12px;
        }

        .password-requirements h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
        }

        .password-requirements li {
            padding: 3px 0;
            color: #666;
        }

        .password-requirements li::before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: bold;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4CAF50, #8BC34A);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .strength-weak { width: 33%; background: #dc3545; }
        .strength-medium { width: 66%; background: #ffc107; }
        .strength-strong { width: 100%; background: #4CAF50; }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="header-section">
            <div class="icon-container">
                <ion-icon name="key-outline"></ion-icon>
            </div>
            <h1>Reset Password</h1>
            <p>Create a strong, secure password for your account.</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                    <input id="password" 
                           type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           placeholder="Enter new password"
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggle-icon-1')">
                        <ion-icon name="eye-outline" id="toggle-icon-1"></ion-icon>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <div class="input-wrapper">
                    <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                    <input id="password_confirmation" 
                           type="password" 
                           class="form-control" 
                           name="password_confirmation" 
                           placeholder="Confirm new password"
                           required>
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggle-icon-2')">
                        <ion-icon name="eye-outline" id="toggle-icon-2"></ion-icon>
                    </button>
                </div>
            </div>

            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Mix of uppercase and lowercase letters</li>
                    <li>Include numbers</li>
                    <li>Use special characters (@, #, $, etc.)</li>
                </ul>
            </div>

            <button type="submit" class="submit-btn">
                Reset Password
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Back to Login
            </a>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordField = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.setAttribute('name', 'eye-off-outline');
            } else {
                passwordField.type = 'password';
                toggleIcon.setAttribute('name', 'eye-outline');
            }
        }

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            // Check length
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Check for lowercase and uppercase
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;

            // Check for numbers
            if (/\d/.test(password)) strength++;

            // Check for special characters
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>s