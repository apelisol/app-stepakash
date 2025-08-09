<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    try {
        Mail::raw('Test email content', function($message) {
            $message->to('livingstoneapeli@gmail.com')
                    ->subject('Test Email from StepaKash');
        });
        
        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});
