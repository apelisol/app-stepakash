<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Money Received</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .highlight { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .details table { width: 100%; }
        .details td { padding: 8px 0; border-bottom: 1px solid #eee; }
        .details td:first-child { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Money Received!</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $recipient->fullname }},</p>
            
            <div class="highlight">
                <p>Great news! You have received money in your wallet.</p>
                <p class="amount">KES {{ number_format($amount, 2) }}</p>
            </div>
            
            <div class="details">
                <h3>Transaction Details</h3>
                <table>
                    <tr>
                        <td>Amount Received:</td>
                        <td>KES {{ number_format($amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>From:</td>
                        <td>{{ $sender->fullname }} ({{ $sender->phone }})</td>
                    </tr>
                    <tr>
                        <td>Transaction ID:</td>
                        <td>{{ $transactionId }}</td>
                    </tr>
                    <tr>
                        <td>Date & Time:</td>
                        <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            
            <p>The money has been added to your wallet balance and is available for immediate use.</p>
            
            <p>You can:</p>
            <ul>
                <li>Send money to other users</li>
                <li>Withdraw to your M-Pesa account</li>
                <li>Use for other services on our platform</li>
            </ul>
            
            <p>Thank you for using our service!</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} StepAKash. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>