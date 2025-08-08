@component('mail::message')
# Deposit Received

Dear {{ $customerName }},

Your StepaKash wallet has been credited with **KES {{ number_format($amount, 2) }}**.

**Transaction Details:**  
- **Reference:** {{ $mpesaCode }}  
- **Date:** {{ $transactionDate }}  
- **New Balance:** KES {{ number_format($balance, 2) }}  

@component('mail::button', ['url' => $dashboardUrl])
View Wallet
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent