<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Money Transfer Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2c5aa0; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
        .highlight { background: #e8f4f8; padding: 15px; border-left: 4px solid #2c5aa0; margin: 15px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #2c5aa0; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .details table { width: 100%; }
        .details td { padding: 8px 0; border-bottom: 1px solid #eee; }
        .details td:first-child { font-weight: bold; width: 40%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Money Transfer Confirmation</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $sender->fullname }},</p>
            
            <div class="highlight">
                <p>Your money transfer has been {{ $isExternal ? 'initiated' : 'completed' }} successfully!</p>
                <p class="amount">KES {{ number_format($amount, 2) }}</p>
            </div>
            
            <div class="details">
                <h3>Transfer Details</h3>
                <table>
                    <tr>
                        <td>Amount Sent:</td>
                        <td>KES {{ number_format($amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Transaction Fee:</td>
                        <td>KES {{ number_format($fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Deducted:</td>
                        <td>KES {{ number_format($amount + $fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Recipient:</td>
                        <td>
                            @if($isExternal)
                                {{ $recipientName ?? $recipientPhone }} ({{ $recipientPhone }})
                            @else
                                {{ $recipient->fullname }} ({{ $recipient->phone }})
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>Transfer Type:</td>
                        <td>{{ $isExternal ? 'External Transfer' : 'Internal Transfer' }}</td>
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
            
            @if($isExternal)
                <div class="highlight">
                    <p><strong>Note:</strong> The recipient will receive the money to their mobile money account shortly. You will receive another confirmation once the transfer is completed.</p>
                </div>
            @endif
            
            <p>Thank you for using our money transfer service!</p>
            
            <p>If you have any questions or concerns, please contact our customer support.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} StepAKash. All rights reserved.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
