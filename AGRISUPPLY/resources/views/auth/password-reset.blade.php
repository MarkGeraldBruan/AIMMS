<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4CAF50, #8BC34A);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .code-container {
            background: #f8f9fa;
            border: 2px dashed #4CAF50;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #4CAF50;
            font-family: 'Courier New', monospace;
        }
        .code-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-box strong {
            color: #856404;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .warning {
            color: #dc3545;
            font-size: 13px;
            margin-top: 20px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Password Reset Request</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Hello {{ $user->name }},</p>
            
            <p>We received a request to reset your password for your {{ config('app.name') }} account. Use the verification code below to proceed:</p>
            
            <div class="code-container">
                <div class="code-label">Your Verification Code</div>
                <div class="code">{{ $code }}</div>
            </div>
            
            <div class="info-box">
                <strong>⚠️ Important Information:</strong>
                <ul>
                    <li>This code will expire in <strong>15 minutes</strong></li>
                    <li>Enter this code on the verification page</li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this, please ignore this email</li>
                </ul>
            </div>
            
            <p>If you're having trouble, you can request a new code from the password reset page.</p>
            
            <p class="warning">
                <strong>Security Notice:</strong> If you did not request a password reset, please ignore this email. Your account is secure and no changes have been made.
            </p>
            
            <p>Best regards,<br>
            {{ config('app.name') }} Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>