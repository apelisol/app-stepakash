@php
    $firstName = explode(' ', $customer->fullname)[0];
@endphp
Dear {{ $firstName }}, your StepaKash OTP is: {{ $otp }}. Valid for 5 minutes. Use it to reset your password.