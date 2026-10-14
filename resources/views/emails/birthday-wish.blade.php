{{-- resources/views/emails/birthday-wish.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Happy {{ ucfirst($type) }}!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
        }
        .header .emoji {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            font-style: italic;
            border-radius: 8px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="emoji">
                @if($type == 'birthday')
                    🎂 🎉 🎈
                @else
                    💍 💕 🎊
                @endif
            </div>
            <h1>Happy {{ ucfirst($type) }}!</h1>
            <p>From the entire team</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                <strong>Dear {{ $employee->name }},</strong>
            </div>
            
            <div class="message-box">
                <p>{{ $message }}</p>
            </div>
            
            @if($type == 'birthday')
                <p>May your day be filled with joy, laughter, and wonderful memories. Wishing you a fantastic year ahead!</p>
                <p>On your special day, we want to thank you for being an amazing part of our team.</p>
                @if($employee->age)
                    <p>Here's to celebrating your {{ $employee->age }}th birthday! 🥂</p>
                @endif
            @else
                <p>Congratulations on another year of love and togetherness! Wishing you a wonderful celebration.</p>
                <p>Thank you for your dedication and commitment to our organization.</p>
                <p>May your years ahead be filled with continued success and happiness.</p>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/employee/dashboard') }}" class="button">
                    View Your Dashboard
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from Employee Management System</p>
            <p>&copy; {{ date('Y') }} Employee Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>