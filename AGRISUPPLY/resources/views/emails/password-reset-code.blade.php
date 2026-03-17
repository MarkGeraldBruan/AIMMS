<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Verification Code</title>
</head>
<body>
    <h1>Password Reset Verification</h1>
    <p>Hello {{ $user->name }},</p>
    <p>You have requested to reset your password. Your verification code is:</p>
    <h2>{{ $code }}</h2>
    <p>This code will expire in 15 minutes. Please enter it on the verification page to proceed with resetting your password.</p>
    <p>If you did not request this password reset, please ignore this email.</p>
    <br>
    <p>Best regards,<br>The AGRISUPPLY Team</p>
</body>
</html>
