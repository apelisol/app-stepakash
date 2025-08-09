@component('mail::message')
# Welcome to {{ config('app.name') }}

Hello {{ $user->fullname }},

Thank you for registering with {{ config('app.name') }}! We're excited to have you on board and look forward to serving you.

## Your Account Details
- **Wallet ID:** {{ $user->wallet_id }}
- **Email:** {{ $user->email }}

## Getting Started
1. Log in to your account using your email and password
2. Explore our services and features
3. Contact support if you need any assistance

## Security Tips
- Never share your password with anyone
- Always log out after using a shared computer
- Contact us immediately if you notice any suspicious activity

If you did not create an account, please contact our support team immediately.

Thanks,<br>
The {{ config('app.name') }} Team
@endcomponent
