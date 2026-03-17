<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ __('Register') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            /* Replace with your actual background image */
            background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 50%, #FFC107 100%);
            background: url('{{ asset('assets/img/BgForLoginAndRegister.png') }}') no-repeat center center fixed;
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px 0;
        }

        /* Background overlay effect */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(76, 175, 80, 0.3) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(255, 193, 7, 0.3) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(139, 195, 74, 0.2) 0%, transparent 70%);
            z-index: 1;
        }

        /* Header section styles */
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            z-index: 2;
            position: relative;
        }

        .header-section .logo {
            margin-bottom: 20px;
        }

        .header-section .logo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .header-section .logo-fallback {
            width: 80px;
            height: 80px;
            background: #2e7d32;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .header-section .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 12px;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .header-section .subtitle {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1rem;
            line-height: 1.5;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        .header-section .subtitle strong {
            color: white;
            font-weight: 600;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.2),
                0 8px 25px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
            min-width: 0; /* Ensures flexbox children can shrink */
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1.2rem;
            z-index: 3;
            width: 20px;
            height: 20px;
        }

        .form-control {
            width: 100%;
            padding: 15px 50px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: #fafafa;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
            background: white;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 8px;
            font-size: 0.875rem;
            color: #dc3545;
            background: #f8d7da;
            padding: 8px 12px;
            border-radius: 6px;
            border-left: 4px solid #dc3545;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 5px;
            z-index: 3;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #2e7d32;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 0.8rem;
        }

        .strength-bar {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin: 5px 0;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background-color: #dc3545; }
        .strength-medium { background-color: #ffc107; }
        .strength-strong { background-color: #28a745; }

        .register-btn {
            width: 100%;
            background: #2e7d32;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 20px;
        }

        .register-btn:hover {
            background: #1b5e20;
            transform: translateY(-1px);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .register-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #999;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
            z-index: 1;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        .google-btn {
            width: 100%;
            background: white;
            border: 2px solid #e0e0e0;
            padding: 14px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            text-decoration: none;
            color: #333;
        }

        .google-btn:hover {
            border-color: #2e7d32;
            box-shadow: 0 2px 8px rgba(46, 125, 50, 0.1);
            text-decoration: none;
            color: #333;
        }

        .google-icon {
            width: 20px;
            height: 20px;
        }

        .signin-text {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .signin-link {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .signin-link:hover {
            color: #1b5e20;
            text-decoration: underline;
        }

        .terms-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
        }

        .terms-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2e7d32;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .terms-checkbox label {
            margin: 0 !important;
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;
        }

        .terms-checkbox a {
            color: #2e7d32;
            text-decoration: none;
        }

        .terms-checkbox a:hover {
            text-decoration: underline;
        }

            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .form-row {
                    flex-direction: column;
                    gap: 0;
                }

                .form-control {
                    padding: 14px 40px 14px 40px;
                }
            }

            @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .header-section .welcome-title {
                font-size: 2rem;
            }
            
            .header-section .subtitle {
                font-size: 0.9rem;
            }
            
            .header-section .logo img,
            .header-section .logo-fallback {
                width: 70px;
                height: 70px;
            }
            
            .register-container {
                padding: 30px 25px;
            }

            .form-control {
                padding: 14px 40px 14px 40px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        /* Navigation styles for top links */
        .nav-links {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            margin-left: 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Logo, Title and Subtitle outside the container -->
    <div class="header-section">
        <div class="logo">
            @if(file_exists(public_path('assets/img/atirtc1logo.jpg')))
                <img src="{{ asset('assets/img/sample.png') }}" alt="{{ config('app.name', 'AGRISUPPLY') }}">
                <a style="color: white">X</a>
                <img src="{{ asset('assets/img/atirtc1logo.jpg') }}" alt="{{ config('app.name', 'AGRISUPPLY') }}">
            @else
                <div class="logo-fallback">
                    {{ substr(config('app.name', 'AGRISUPPLY'), 0, 2) }}
                </div>
            @endif
        </div>
        
        <h1 class="welcome-title">{{ __('CREATE ACCOUNT') }}</h1>
        <p class="subtitle"><strong>{{ __('Sign up') }}</strong><br>{{ __('Fill in your details to create your new account.') }}</p>
    </div>

    <div class="register-container">
        
        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('Full Name') }}</label>
                <div class="input-wrapper">
                    <ion-icon name="person-outline" class="input-icon"></ion-icon>
                    <input id="name" 
                           type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="John Doe"
                           required 
                           autocomplete="name" 
                           autofocus>

                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email Address') }}</label>
                <div class="input-wrapper">
                    <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                    <input id="email" 
                           type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="name@gmail.com"
                           required 
                           autocomplete="email">

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <input id="password" 
                               type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" 
                               placeholder="Password"
                               required 
                               autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggle-icon-1')">
                            <ion-icon name="eye-outline" id="toggle-icon-1"></ion-icon>
                        </button>

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="password-strength" id="password-strength" style="display: none;">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <span id="strength-text">Password strength</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <input id="password-confirm" 
                               type="password" 
                               class="form-control" 
                               name="password_confirmation" 
                               placeholder="Confirm Password"
                               required 
                               autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password-confirm', 'toggle-icon-2')">
                            <ion-icon name="eye-outline" id="toggle-icon-2"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>

            <div class="terms-checkbox">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">
                    {{ __('I agree to the') }} <a href="#" target="_blank">{{ __('Terms of Service') }}</a> 
                    {{ __('and') }} <a href="#" target="_blank">{{ __('Privacy Policy') }}</a>
                </label>
            </div>

            <button type="submit" class="register-btn" id="register-btn">
                {{ __('Create Account') }}
            </button>



            <p class="signin-text">
                {{ __("Back to") }}
                <a href="{{ route('login') }}" class="signin-link">{{ __('Login') }}</a>
            </p>
        </form>
    </div>

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
        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('password-strength');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            
            if (password.length === 0) {
                strengthIndicator.style.display = 'none';
                return;
            }
            
            strengthIndicator.style.display = 'block';
            
            let strength = 0;
            let requirements = [];
            
            // Check length
            if (password.length >= 8) strength++;
            else requirements.push('at least 8 characters');
            
            // Check for lowercase
            if (/[a-z]/.test(password)) strength++;
            else requirements.push('lowercase letter');
            
            // Check for uppercase
            if (/[A-Z]/.test(password)) strength++;
            else requirements.push('uppercase letter');
            
            // Check for numbers
            if (/\d/.test(password)) strength++;
            else requirements.push('number');
            
            // Check for special characters
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;
            else requirements.push('special character');
            
            let strengthClass = '';
            let strengthLabel = '';
            let width = '0%';
            
            if (strength <= 2) {
                strengthClass = 'strength-weak';
                strengthLabel = 'Weak';
                width = '33%';
            } else if (strength <= 3) {
                strengthClass = 'strength-medium';
                strengthLabel = 'Medium';
                width = '66%';
            } else {
                strengthClass = 'strength-strong';
                strengthLabel = 'Strong';
                width = '100%';
            }
            
            strengthFill.className = `strength-fill ${strengthClass}`;
            strengthFill.style.width = width;
            
            if (requirements.length > 0) {
                strengthText.textContent = `${strengthLabel} - Need: ${requirements.join(', ')}`;
            } else {
                strengthText.textContent = `${strengthLabel} - Great password!`;
            }
        }

        // Password confirmation checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password-confirm').value;
            const confirmInput = document.getElementById('password-confirm');
            
            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    confirmInput.classList.add('is-invalid');
                } else {
                    confirmInput.classList.remove('is-invalid');
                }
            }
        }

        // Add event listeners
        document.getElementById('password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });

        document.getElementById('password-confirm').addEventListener('input', checkPasswordMatch);

        // Add interactive effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Clear validation errors on input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            });
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password-confirm').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy!');
                return false;
            }
        });
    </script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    @include('layouts.core.footer')
</body>
</html>
