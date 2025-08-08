<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MpesaService
{
    protected $config;
    protected $consumerKey;
    protected $consumerSecret;
    protected $passkey;
    protected $shortCode;
    protected $initiatorName;
    protected $initiatorPassword;

    public function __construct()
    {
        $this->config = config('mpesa');
        $this->consumerKey = $this->config['consumer_key'];
        $this->consumerSecret = $this->config['consumer_secret'];
        $this->passkey = $this->config['passkey'];
        $this->shortCode = $this->config['short_code'];
        $this->initiatorName = $this->config['initiator_name'];
        $this->initiatorPassword = $this->config['initiator_password'];
    }

    /**
     * Generate OAuth token
     */
    public function generateToken()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret)
            ])->get('https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('M-Pesa Token Generation Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa Token Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initiate STK push
     */
    public function stkPush($phone, $amount, $accountReference, $callbackUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortCode . $this->passkey . $timestamp);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $this->shortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $this->shortCode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => 'Wallet Deposit'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa STK Push Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'STK push failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Initiate B2C payment
     */
    public function b2cRequest($phone, $amount, $transactionId, $callbackUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        $securityCredential = $this->generateSecurityCredential();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/b2c/v1/paymentrequest', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $securityCredential,
                'CommandID' => 'SalaryPayment',
                'Amount' => $amount,
                'PartyA' => $this->shortCode,
                'PartyB' => $phone,
                'Remarks' => 'Wallet Withdrawal',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => 'Wallet Withdrawal'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa B2C Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'B2C request failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa B2C Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Generate security credential for B2C
     */
    protected function generateSecurityCredential()
    {
        $publicKeyPath = Storage::path('certificates/mpesa_cert.cer');
        $publicKey = file_get_contents($publicKeyPath);
        
        openssl_public_encrypt($this->initiatorPassword, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }


    protected function b2bRequest($phone, $amount, $transactionId, $callbackUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/b2b/v1/paymentrequest', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'BusinessPayBill',
                'Amount' => $amount,
                'PartyA' => $this->shortCode,
                'PartyB' => $phone,
                'Remarks' => 'Business Payment',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => $transactionId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa B2B Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'B2B request failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa B2B Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }
}


    