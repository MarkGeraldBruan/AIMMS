<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Verify Code</title>
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

        .verify-container {
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

        .email-sent-to {
            background: #e8f5e9;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
            color: #2e7d32;
        }

        .email-sent-to strong {
            color: #1b5e20;
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

        .code-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .code-input {
            width: 50px;
            height: 55px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .code-input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .code-input.is-invalid {
            border-color: #dc3545;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .resend-section {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .resend-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .resend-form {
            display: inline;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #4CAF50;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            text-decoration: underline;
        }

        .resend-btn:hover {
            color: #296218;
        }

        .resend-btn:disabled {
            color: #999;
            cursor: not-allowed;
        }

        .timer {
            color: #999;
            font-size: 13px;
            margin-top: 5px;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
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

        .back-link a:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none;
        }

        .info-text {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="verify-container">
        <div class="header-section">
            <div class="icon-container">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
            </div>
            <h1>Verify Your Email</h1>
            <p>Enter the 6-digit code we sent to your email</p>
        </div>

        @if(session('email'))
            <div class="email-sent-to">
                📧 Code sent to: <strong>{{ session('email') }}</strong>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify') }}" id="verifyForm">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') }}">
            
            <div class="form-group">
                <label>Verification Code</label>
                <div class="code-inputs">
                    <input type="text" maxlength="1" class="code-input" id="code1" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="code-input" id="code2" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="code-input" id="code3" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="code-input" id="code4" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="code-input" id="code5" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="code-input" id="code6" pattern="[0-9]" autocomplete="off">
                </div>
                <input type="hidden" name="code" id="fullCode">
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                Verify Code
            </button>
        </form>

        <div class="resend-section">
            <p class="resend-text">Didn't receive the code?</p>
            <form method="POST" action="{{ route('password.resend') }}" class="resend-form" id="resendForm">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}">
                <button type="submit" class="resend-btn" id="resendBtn">
                    Resend Code
                </button>
            </form>
            <div class="timer hidden" id="timer">
                Please wait <span id="countdown">60</span> seconds
            </div>
        </div>

        <div class="back-link">
            <a href="{{ route('password.request') }}">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Use different email
            </a>
        </div>

        <p class="info-text">
            Code expires in 15 minutes
        </p>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
        // Auto-focus and auto-tab between code inputs
        const codeInputs = document.querySelectorAll('.code-input');
        const fullCodeInput = document.getElementById('fullCode');
        const submitBtn = document.getElementById('submitBtn');
        const verifyForm = document.getElementById('verifyForm');

        codeInputs.forEach((input, index) => {
            // Only allow numbers
            input.addEventListener('input', function(e) {
                if (this.value && !/^\d$/.test(this.value)) {
                    this.value = '';
                    return;
                }

                // Move to next input
                if (this.value && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }

                // Update full code
                updateFullCode();
            });

            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                
                if (/^\d{6}$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (codeInputs[i]) {
                            codeInputs[i].value = char;
                        }
                    });
                    codeInputs[5].focus();
                    updateFullCode();
                }
            });
        });

        function updateFullCode() {
            const code = Array.from(codeInputs).map(input => input.value).join('');
            fullCodeInput.value = code;
            
            // Enable submit button when all digits are entered
            submitBtn.disabled = code.length !== 6;
        }

        // Focus first input on load
        codeInputs[0].focus();

        // Resend timer
        const resendBtn = document.getElementById('resendBtn');
        const resendForm = document.getElementById('resendForm');
        const timer = document.getElementById('timer');
        const countdown = document.getElementById('countdown');
        let timeLeft = 60;
        let timerInterval;

        function startResendTimer() {
            resendBtn.disabled = true;
            timer.classList.remove('hidden');
            timeLeft = 60;
            
            timerInterval = setInterval(() => {
                timeLeft--;
                countdown.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    timer.classList.add('hidden');
                }
            }, 1000);
        }

        // Start timer if code was just sent
        @if(session('success'))
            startResendTimer();
        @endif

        resendForm.addEventListener('submit', function() {
            startResendTimer();
        });
    </script>
</body>
</html>