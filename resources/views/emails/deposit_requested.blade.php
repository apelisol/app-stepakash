@component('mail::message')
# M-Pesa Deposit Initiated

Dear {{ $customerName }},

We've received your request to deposit **KES {{ number_format($amount, 2) }}** to your StepaKash wallet.

**Transaction Reference:** {{ $transactionRef }}

Please complete the payment on your mobile phone to update your wallet balance.

@component('mail::button', ['url' => $dashboardUrl])
View Wallet
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent