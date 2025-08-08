<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Outbox;

class SmsService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('sms');
    }

    /**
     * Send SMS message
     *
     * @param string $mobile Phone number
     * @param string $message SMS content
     * @return array
     */
    public function sendSms($mobile, $message)
    {
        $phone = $this->formatPhoneNumber($mobile);
        $currentDateTime = now()->setTimezone('Africa/Nairobi');

        $response = Http::withHeaders([
            'apikey' => $this->config['api_key'],
            'cache-control' => 'no-cache',
            'content-type' => 'application/x-www-form-urlencoded'
        ])->post($this->config['api_url'], [
            'userid' => $this->config['userid'],
            'password' => $this->config['password'],
            'mobile' => $phone,
            'msg' => $message,
            'senderid' => $this->config['senderid'],
            'msgType' => 'text',
            'duplicatecheck' => 'true',
            'output' => 'json',
            'sendMethod' => 'quick'
        ]);

        // Log the SMS in outbox
        Outbox::create([
            'receiver' => $phone,
            'message' => $message,
            'created_on' => $currentDateTime,
            'status' => $response->successful() ? 'sent' : 'failed'
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->json(),
            'error' => $response->failed() ? $response->body() : null
        ];
    }

    /**
     * Format phone number to 254 format
     */
    protected function formatPhoneNumber($mobile)
    {
        return preg_replace('/^(?:\+?254|0)?/', '254', $mobile);
    }
}
