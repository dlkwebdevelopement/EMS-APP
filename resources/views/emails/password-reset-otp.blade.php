<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP Verification</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; color: #333; }
        .card { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        .title { color: #0284c7; font-size: 22px; font-weight: bold; margin: 0; }
        .otp-box { background: #f0f9ff; border: 2px dashed #0284c7; border-radius: 8px; text-align: center; padding: 18px; margin: 25px 0; }
        .otp-code { font-size: 34px; font-weight: 900; letter-spacing: 6px; color: #0369a1; margin: 0; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2 class="title">DLK Employee Portal</h2>
            <p style="margin-top: 5px; color: #6b7280; font-size: 14px;">Password Reset Verification Code</p>
        </div>
        <p>Dear <strong>{{ $employee->name }}</strong>,</p>
        <p>You requested to reset your password. Use the following 6-digit One-Time Password (OTP) to verify your request on your official office email:</p>
        
        <div class="otp-box">
            <h1 class="otp-code">{{ $otp }}</h1>
        </div>

        <p style="font-size: 13px; color: #6b7280;">This OTP is valid for <strong>15 minutes</strong>. Do not share this OTP code with anyone for security purposes.</p>
        <p style="font-size: 13px; color: #6b7280;">If you did not request a password reset, please contact IT Support / HR immediately.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} DLK Technologies. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
