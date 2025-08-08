<!DOCTYPE html>
<html>

<head>
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .otp-code {
            background: #f8f9fa;
            padding: 15px;
            font-size: 24px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
            color: #2c3e50;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="header">Password Reset Request</h2>

        <p>Dear {{ $customer->fullname }},</p>

        <p>We received a request to reset your StepaKash account password. Please use the following OTP to proceed:</p>

        <div class="otp-code">{{ $otp }}</div>

        <p>This OTP is valid for 5 minutes. If you didn't request this password reset, please ignore this email or contact our support team immediately.</p>

        <div class="footer">
            <p>Thank you,<br>
                The StepaKash Team</p>
            <p><small>This is an automated message. Please do not reply directly to this email.</small></p>
        </div>
    </div>
</body>

</html>