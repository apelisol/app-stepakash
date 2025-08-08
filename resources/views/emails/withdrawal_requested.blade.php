@component('mail::message')
# Withdrawal Processing

Dear {{ $customerName }},

Your request to withdraw **KES {{ number_format($amount, 2) }}** to M-Pesa is being processed.

**Transaction Reference:** {{ $transactionRef }}

You'll receive another notification once the transaction is completed.

@component('mail::button', ['url' => $dashboardUrl])
View Wallet
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent