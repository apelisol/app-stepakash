<?php

return [
    'app_id' => env('DERIV_APP_ID'),
    'redirect_uri' => env('DERIV_REDIRECT_URI'),
    'scope' => 'read,trade,trading_information,payments',
    'api_url' => 'https://api.deriv.com',
    'oauth_url' => 'https://oauth.deriv.com',
];
