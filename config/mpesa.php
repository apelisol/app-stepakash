<?php

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'short_code' => env('MPESA_SHORT_CODE', ''),
    'initiator_name' => env('MPESA_INITIATOR_NAME', ''),
    'initiator_password' => env('MPESA_INITIATOR_PASSWORD', ''),
    'environment' => env('MPESA_ENVIRONMENT', 'production'), // 'sandbox' or 'production'
];