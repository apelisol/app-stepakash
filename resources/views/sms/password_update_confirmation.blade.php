@php
$firstName = explode(' ', $customer->fullname)[0];
@endphp
Dear {{ $firstName }}, your StepaKash password was successfully updated on {{ now()->format('j M Y, g:i a') }}.

If you didn't make this change, please contact our support team immediately at +254703416091.