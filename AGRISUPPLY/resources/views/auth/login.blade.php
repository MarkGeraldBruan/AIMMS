<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - {{ __('Login') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
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
        overflow-y: auto;
        padding: 20px 0 120px 0;
    }

    /* Form row with forgot password link */
    .form-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .forgot-password-link {
        color: #4CAF50;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .forgot-password-link:hover {
        color: #296218;
        text-decoration: underline;
    }

    /* Additional styles for checkbox wrapper */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .checkbox-wrapper label {
        font-size: 13px;
        color: #333;
        cursor: pointer;
        user-select: none;
    }

    /* Terms of Service and Privacy Policy link styles */
    .terms-privacy-link {
        margin-bottom: 20px;
        text-align: center;
    }

    .terms-privacy-link a {
        font-size: 13px;
        color: #555;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .terms-privacy-link a:hover {
        color: #4CAF50;
    }

    .highlight-link {
        color: #4CAF50;
        font-weight: 600;
        text-decoration: underline;
    }

    .highlight-link:hover {
        color: #296218;
    }

    /* Terms and Privacy Modal Styles */
    .terms-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
    }

    .terms-modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        border-radius: 12px;
        width: 80%;
        max-width: 800px;
        max-height: 80vh;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .terms-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa;
        border-radius: 12px 12px 0 0;
    }

    .terms-modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #333;
        font-weight: 600;
    }

    .terms-modal-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s ease;
        line-height: 1;
    }

    .terms-modal-close:hover {
        color: #4CAF50;
    }

    .terms-modal-body {
        padding: 25px;
        max-height: 60vh;
        overflow-y: auto;
    }

    .terms-tabs {
        display: flex;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }

    .terms-tab {
        padding: 12px 24px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 500;
        color: #666;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s ease;
    }

    .terms-tab:hover {
        color: #4CAF50;
    }

    .terms-tab.active {
        color: #4CAF50;
        border-bottom-color: #4CAF50;
    }

    .terms-content {
        display: none;
        line-height: 1.7;
        color: #444;
    }

    .terms-content.active {
        display: block;
    }

    .terms-content h3 {
        color: #333;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .terms-content h3:first-child {
        margin-top: 0;
    }

    .terms-content p {
        margin-bottom: 15px;
    }

    .terms-content ul {
        padding-left: 20px;
        margin-bottom: 15px;
    }

    .terms-content li {
        margin-bottom: 8px;
    }

    .terms-accept-section {
        padding: 20px 25px;
        border-top: 1px solid #e9ecef;
        background-color: #f8f9fa;
        border-radius: 0 0 12px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .terms-accept-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .terms-accept-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #4CAF50;
        cursor: pointer;
    }

    .terms-accept-checkbox label {
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }

    .terms-accept-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 10px 30px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .terms-accept-btn:hover {
        background-color: #45a049;
    }

    .terms-accept-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
</style>

</head>

<body>
    <!-- Logo, Title and Subtitle outside the container -->
    <div class="header-section">
        
        <h1 class="welcome-title">{{ __('AIMMS') }}</h1>
        <p class="subtitle">{{ __('Agricultural Training Institute - Regional Training Center 1 Monitoring Management System') }}</p>
    </div>

    <div class="login-container">
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">{{ __('Enter your email address') }}</label>
                <div class="input-wrapper">
                    <ion-icon name="person-outline" class="input-icon"></ion-icon>
                    <input id="email" 
                           type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="name@gmail.com"
                           required 
                           autocomplete="email" 
                           autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">{{ __('Enter your password') }}</label>
                <div class="input-wrapper">
                    <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                    <input id="password" 
                           type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           name="password" 
                           placeholder="mypassword"
                           required 
                           autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <ion-icon name="eye-outline" id="toggle-icon"></ion-icon>
                    </button>

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <!-- Terms of Service and Privacy Policy -->
            <div class="terms-privacy-link">
                <a href="#" class="terms-privacy-link" onclick="event.preventDefault(); showTermsPrivacyModal();">
                 <span class="highlight-link">Terms of Service</span> and <span class="highlight-link">Privacy Policy</span>
                </a>
            </div>

            <div class="form-row">
                <div class="checkbox-wrapper">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">{{ __('Remember me') }}</label>
                </div>
                
                <a href="{{ route('password.request') }}" class="forgot-password-link">
                    {{ __('Forgot Password?') }}
                </a>
            </div>

            <button type="submit" class="login-btn">
                {{ __('Log In') }}
            </button>


        </form>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="error-modal">
        <div class="error-modal-content">
            <div class="error-modal-icon">
                <ion-icon name="close-circle-outline"></ion-icon>
            </div>
            <h3 class="error-modal-title">Login Failed</h3>
            <p class="error-modal-message">Invalid email or password. Please try again.</p>
            <button class="error-modal-close" onclick="closeErrorModal()">Try Again</button>
        </div>
    </div>

    <script>
        // Check for authentication errors on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there are any authentication errors
            const hasEmailError = document.querySelector('.form-control[name="email"].is-invalid');
            const hasPasswordError = document.querySelector('.form-control[name="password"].is-invalid');
            
            // If there are authentication errors, show the modal instead of inline errors
            if (hasEmailError || hasPasswordError) {
                showErrorModal();
            }
        });

        function showErrorModal() {
            document.getElementById('errorModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeErrorModal() {
            document.getElementById('errorModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            
            // Clear the form and remove error classes
            document.getElementById('email').classList.remove('is-invalid');
            document.getElementById('password').classList.remove('is-invalid');
            document.getElementById('password').value = ''; // Clear password field
            document.getElementById('email').focus(); // Focus back to email field
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('errorModal');
            if (event.target == modal) {
                closeErrorModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeErrorModal();
            }
        });

        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.setAttribute('name', 'eye-off-outline');
            } else {
                passwordField.type = 'password';
                toggleIcon.setAttribute('name', 'eye-outline');
            }
        }

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
    </script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Terms and Privacy Policy Modal -->
    <div id="termsModal" class="terms-modal">
        <div class="terms-modal-content">
            <div class="terms-modal-header">
                <h2>Terms of Service & Privacy Policy</h2>
                <span class="terms-modal-close" onclick="closeTermsModal()">&times;</span>
            </div>
            <div class="terms-modal-body">
                <div class="terms-tabs">
                    <div class="terms-tab active" onclick="switchTab('terms')">Terms of Service</div>
                    <div class="terms-tab" onclick="switchTab('privacy')">Privacy Policy</div>
                </div>
                
                <!-- Terms of Service Content -->
                <div id="terms-content" class="terms-content active">
                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing and using this system, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h3>2. Description of Service</h3>
                    <p>This is an agricultural supply management system designed for tracking, managing, and reporting agricultural supplies and equipment.</p>
                    
                    <h3>3. User Responsibilities</h3>
                    <ul>
                        <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                        <li>You agree to use this system only for lawful purposes</li>
                        <li>You agree not to share your account credentials with others</li>
                    </ul>
                    
                    <h3>4. Intellectual Property</h3>
                    <p>All content, features, and functionality of this system are owned by the organization and are protected by international copyright laws.</p>
                    
                    <h3>5. Limitation of Liability</h3>
                    <p>The organization shall not be liable for any indirect, incidental, special, or consequential damages arising from the use of this system.</p>
                </div>
                
                <!-- Privacy Policy Content -->
                <div id="privacy-content" class="terms-content">
                    <h3>1. Information We Collect</h3>
                    <p>We collect personal information that you provide to us, including but not limited to your name, email address, and organizational affiliation.</p>
                    
                    <h3>2. How We Use Your Information</h3>
                    <ul>
                        <li>To provide and maintain our services</li>
                        <li>To notify you about changes to our services</li>
                        <li>To provide customer support</li>
                        <li>To gather analysis or valuable information so that we can improve our services</li>
                    </ul>
                    
                    <h3>3. Data Security</h3>
                    <p>We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    
                    <h3>4. Data Retention</h3>
                    <p>We will retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy.</p>
                    
                    <h3>5. Your Rights</h3>
                    <p>You have the right to access, update, or delete your personal information at any time. Please contact the system administrator for assistance.</p>
                </div>
            </div>
            <div class="terms-accept-section">
                <div class="terms-accept-checkbox">
                    <input type="checkbox" id="acceptTermsCheckbox" onchange="toggleAcceptButton()">
                    <label for="acceptTermsCheckbox">I have read and agree to the Terms of Service and Privacy Policy</label>
                </div>
                <button type="button" class="terms-accept-btn" id="acceptTermsBtn" onclick="acceptTerms()" disabled>
                    Accept & Continue
                </button>
            </div>
        </div>
    </div>

    <script>
        // Show Terms and Privacy Modal
        function showTermsPrivacyModal() {
            document.getElementById('termsModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Close Terms and Privacy Modal
        function closeTermsModal() {
            document.getElementById('termsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Switch between tabs
        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.terms-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.terms-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            if (tabName === 'terms') {
                document.querySelector('.terms-tab:nth-child(1)').classList.add('active');
                document.getElementById('terms-content').classList.add('active');
            } else {
                document.querySelector('.terms-tab:nth-child(2)').classList.add('active');
                document.getElementById('privacy-content').classList.add('active');
            }
        }

        // Toggle accept button based on checkbox
        function toggleAcceptButton() {
            const checkbox = document.getElementById('acceptTermsCheckbox');
            const btn = document.getElementById('acceptTermsBtn');
            btn.disabled = !checkbox.checked;
        }

        // Accept terms and close modal
        function acceptTerms() {
            closeTermsModal();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('termsModal');
            if (event.target == modal) {
                closeTermsModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTermsModal();
            }
        });
    </script>

    @include('layouts.core.footer')
</body>
</html>