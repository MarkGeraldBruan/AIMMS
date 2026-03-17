
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Credentials</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #296218 0%, #3a7d24 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #296218;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            margin-bottom: 25px;
            color: #555;
        }
        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #296218;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        .credential-item {
            margin: 15px 0;
            padding: 12px;
            background-color: white;
            border-radius: 5px;
            border-left: 4px solid #296218;
        }
        .credential-label {
            font-weight: 600;
            color: #296218;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 16px;
            color: #333;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            align-items: start;
        }
        .warning-icon {
            color: #ff9800;
            font-size: 24px;
            margin-right: 15px;
        }
        .warning-text {
            color: #856404;
            font-size: 14px;
        }
        .login-button {
            display: inline-block;
            background-color: #296218;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
            text-align: center;
        }
        .login-button:hover {
            background-color: #1f4a12;
        }
        .instructions {
            background-color: #e8f5e9;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .instructions h3 {
            color: #296218;
            margin-top: 0;
            font-size: 16px;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
            color: #555;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .footer-links {
            margin-top: 15px;
        }
        .footer-links a {
            color: #296218;
            text-decoration: none;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎉 Welcome to ATI-RTC i!</h1>
        </div>
        
        <div class="email-body">
            <div class="greeting">
                Hello {{ $user->name }},
            </div>
            
            <div class="message">
                <p>Welcome aboard! An account has been created for you in our system. Below are your login credentials to access your account.</p>
            </div>
            
            <div class="credentials-box">
                <div class="credential-item">
                    <div class="credential-label">📧 Email / Username</div>
                    <div class="credential-value">{{ $user->email }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">🔑 Password</div>
                    <div class="credential-value">{{ $plainPassword }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">👤 Role</div>
                    <div class="credential-value">{{ ucfirst($user->role) }}</div>
                </div>
            </div>
            
            <div class="warning-box">
                <div class="warning-icon">⚠️</div>
                <div class="warning-text">
                    <strong>Important Security Notice:</strong> Please change your password immediately after your first login. Keep your credentials confidential and do not share them with anyone.
                </div>
            </div>
            
            <div class="instructions">
                <h3>📝 How to Get Started:</h3>
                <ol>
                    <li>Click the button below to access the login page</li>
                    <li>Enter your email and the password provided above</li>
                    <li>Navigate to your profile settings to change your password</li>
                    <li>Complete your profile information if needed</li>
                </ol>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="login-button">
                    🚀 Login to Your Account
                </a>
            </div>
            
            <div class="message" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
                <p>Best regards,<br><strong>ATI-RTC i Team</strong></p>
            </div>
        </div>
        
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} ATI-RTC i. All rights reserved.</p>
            <div class="footer-links">
                <a href="{{ url('/') }}">Home</a> |
                <a href="{{ url('/help') }}">Help Center</a> |
                <a href="{{ url('/contact') }}">Contact Us</a>
            </div>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
