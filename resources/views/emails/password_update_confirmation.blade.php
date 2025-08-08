<!DOCTYPE html>
<html>

<head>
    <title>Password Updated</title>
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

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="header">Password Update Confirmation</h2>

        <p>Dear {{ $customer->fullname }},</p>

        <p>This is to confirm that your StepaKash account password was successfully updated on {{ now()->format('F j, Y \a\t g:i A') }}.</p>

        <div class="alert">
            <p>If you didn't make this change, please contact our support team immediately.</p>
        </div>

        <div class="footer">
            <p>Thank you,<br>
                The StepaKash Team</p>
        </div>
    </div>
</body>

</html>