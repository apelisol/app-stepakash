@component('mail::message')
# Withdrawal Completed

Dear {{ $customerName }},

**KES {{ number_format($amount, 2) }}** has been sent to your M-Pesa account.

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