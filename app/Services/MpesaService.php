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
            ])->post('https://api.safaricom.co.ke/mpesa/b2c/v3/paymentrequest', [
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

    /**
     * B2B Payment Request
     */
    public function b2bRequest($receiverShortCode, $amount, $commandID = 'BusinessPayBill', $callbackUrl, $remarks = 'Business Payment', $occasion = '')
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
                'CommandID' => $commandID, // BusinessPayBill, BusinessBuyGoods, B2CAccountTopUp
                'Amount' => $amount,
                'PartyA' => $this->shortCode,
                'PartyB' => $receiverShortCode,
                'Remarks' => $remarks,
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => $occasion
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

    /**
     * Check Account Balance
     */
    public function checkAccountBalance($callbackUrl, $shortCode = null)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        $partyA = $shortCode ?? $this->shortCode;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/accountbalance/v1/query', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'AccountBalance',
                'PartyA' => $partyA,
                'IdentifierType' => '4', // 4 for organization short code
                'Remarks' => 'Account Balance Check',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa Account Balance Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'Account balance check failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa Account Balance Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Buy Airtime via B2C
     */
    public function buyAirtime($phone, $amount, $callbackUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/b2c/v3/paymentrequest', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'BusinessPayment', // For airtime purchase
                'Amount' => $amount,
                'PartyA' => $this->shortCode,
                'PartyB' => $phone,
                'Remarks' => 'Airtime Purchase',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => 'Airtime Top Up'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa Airtime Purchase Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'Airtime purchase failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa Airtime Purchase Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Check Transaction Status
     */
    public function checkTransactionStatus($transactionId, $callbackUrl, $shortCode = null)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        $partyA = $shortCode ?? $this->shortCode;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/transactionstatus/v1/query', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'TransactionStatusQuery',
                'TransactionID' => $transactionId,
                'PartyA' => $partyA,
                'IdentifierType' => '4', // 4 for organization short code
                'Remarks' => 'Transaction Status Check',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => 'Status Check'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa Transaction Status Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'Transaction status check failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa Transaction Status Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Reverse Transaction
     */
    public function reverseTransaction($transactionId, $amount, $callbackUrl, $remarks = 'Transaction Reversal')
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/reversal/v1/request', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'TransactionReversal',
                'TransactionID' => $transactionId,
                'Amount' => $amount,
                'ReceiverParty' => $this->shortCode,
                'RecieverIdentifierType' => '11', // 11 for MSISDN
                'Remarks' => $remarks,
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => 'Reversal'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa Reversal Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'Transaction reversal failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa Reversal Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * STK Push Query
     */
    public function stkPushQuery($checkoutRequestId)
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
            ])->post('https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query', [
                'BusinessShortCode' => $this->shortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa STK Push Query Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'STK push query failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push Query Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Register C2B URLs
     */
    public function registerC2BUrls($validationUrl, $confirmationUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl', [
                'ShortCode' => $this->shortCode,
                'ResponseType' => 'Completed', // or 'Cancelled'
                'ConfirmationURL' => $confirmationUrl,
                'ValidationURL' => $validationUrl
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa C2B URL Registration Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'C2B URL registration failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa C2B URL Registration Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Check Customer Identity
     */
    public function checkCustomerIdentity($phone, $callbackUrl)
    {
        $token = $this->generateToken();
        if (!$token) {
            return ['ResponseCode' => '1', 'errorMessage' => 'Failed to generate token'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ])->post('https://api.safaricom.co.ke/mpesa/checkidentity/v1/processrequest', [
                'InitiatorName' => $this->initiatorName,
                'SecurityCredential' => $this->generateSecurityCredential(),
                'CommandID' => 'CheckIdentity',
                'PartyA' => $this->shortCode,
                'PartyB' => $phone,
                'Remarks' => 'Identity Check',
                'QueueTimeOutURL' => $callbackUrl,
                'ResultURL' => $callbackUrl,
                'Occasion' => 'Customer Identity Verification'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json();
            Log::error('M-Pesa Identity Check Failed: ' . json_encode($error));
            return [
                'ResponseCode' => $error['errorCode'] ?? '1',
                'errorMessage' => $error['errorMessage'] ?? 'Identity check failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa Identity Check Error: ' . $e->getMessage());
            return ['ResponseCode' => '1', 'errorMessage' => $e->getMessage()];
        }
    }

    /**
     * Utility method to format phone number
     */
    public function formatPhoneNumber($phone)
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Add 254 prefix if starts with 0 or 7
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) === '7') {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    /**
     * Validate amount
     */
    public function validateAmount($amount)
    {
        return is_numeric($amount) && $amount > 0 && $amount <= 70000;
    }
}
