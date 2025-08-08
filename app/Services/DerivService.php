<?php

namespace App\Services;

use WebSocket\Client as WebSocketClient;
use Exception;
use Illuminate\Support\Facades\Log;

class DerivService
{
    private $appId;
    private $token;
    private $wsUrl;
    private $client;

    public function __construct()
    {
        $this->appId = config('services.deriv.app_id', 76420);
        $this->token = config('services.deriv.token');
        $this->wsUrl = "wss://ws.derivws.com/websockets/v3?app_id={$this->appId}";
    }

    private function connect()
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $this->client = new WebSocketClient($this->wsUrl, [
                'timeout' => 30,
                'headers' => ['Content-Type' => 'application/json'],
                'context' => $context
            ]);

            // Authenticate
            $this->client->send(json_encode(["authorize" => $this->token]));
            $response = json_decode($this->client->receive(), true);
            
            if (isset($response['error'])) {
                throw new Exception("Authentication failed: " . ($response['error']['message'] ?? 'Unknown error'));
            }

            return true;
        } catch (Exception $e) {
            Log::error('Deriv WebSocket connection error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getBalance()
    {
        try {
            $this->connect();
            
            $this->client->send(json_encode(["balance" => 1, "subscribe" => 1]));
            $response = json_decode($this->client->receive(), true);
            
            return $response['balance'] ?? null;
        } catch (Exception $e) {
            Log::error('Failed to get Deriv balance: ' . $e->getMessage());
            throw $e;
        } finally {
            if (isset($this->client)) {
                $this->client->close();
            }
        }
    }

    /**
     * Request email verification code for withdrawal
     */
    public function requestWithdrawalVerification($email, $amount, $currency = 'USD')
    {
        try {
            $this->connect();
            
            // Request verification code from Deriv
            $request = [
                'verify_email' => $email,
                'type' => 'paymentagent_withdraw',
                'amount' => $amount,
                'currency' => $currency
            ];
            
            $this->client->send(json_encode($request));
            $response = json_decode($this->client->receive(), true);
            
            if (isset($response['error'])) {
                throw new Exception($response['error']['message'] ?? 'Failed to request verification code');
            }
            
            if (!isset($response['verify_email'])) {
                throw new Exception('Invalid response from Deriv API');
            }
            
            return [
                'success' => true,
                'verification_id' => $response['verify_email']['verify_email_id']
            ];
            
        } catch (Exception $e) {
            Log::error('Withdrawal verification request failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            if (isset($this->client)) {
                $this->client->close();
            }
        }
    }
    
    /**
     * Process payment agent withdrawal with verification code
     */
    public function processPaymentAgentWithdrawal($amount, $verificationCode, $paymentAgentId, $verificationId, $description = '')
    {
        try {
            $this->connect();
            
            // Process the withdrawal with the verification code
            $withdrawRequest = [
                'paymentagent_withdraw' => 1,
                'amount' => $amount,
                'currency' => 'USD',
                'paymentagent_loginid' => $paymentAgentId,
                'verification_code' => $verificationCode,
                'verify_email' => $verificationId,
                'description' => $description
            ];
            
            $this->client->send(json_encode($withdrawRequest));
            $response = json_decode($this->client->receive(), true);
            
            if (isset($response['error'])) {
                throw new Exception($response['error']['message'] ?? 'Withdrawal failed');
            }
            
            if (!isset($response['paymentagent_withdraw'])) {
                throw new Exception('Invalid response from Deriv API');
            }
            
            return [
                'success' => true,
                'transaction_id' => $response['transaction_id'] ?? null,
                'paymentagent_name' => $response['paymentagent_name'] ?? null,
                'is_dry_run' => ($response['paymentagent_withdraw'] ?? 0) === 2
            ];
            
        } catch (Exception $e) {
            Log::error('Payment agent withdrawal failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            if (isset($this->client)) {
                $this->client->close();
            }
        }
    }

    public function transferToAccount($loginId, $amount, $description = "Deposit via Stepakash")
    {
        try {
            $this->connect();

            $transferRequest = [
                "paymentagent_transfer" => 1,
                "transfer_to" => $loginId,
                "amount" => $amount,
                "currency" => "USD",
                "description" => $description
            ];

            $this->client->send(json_encode($transferRequest));
            $response = json_decode($this->client->receive(), true);

            if (isset($response['error'])) {
                throw new Exception($response['error']['message'] ?? 'Transfer failed');
            }

            if (!isset($response['paymentagent_transfer'])) {
                throw new Exception('Invalid response from Deriv API');
            }

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error('Deriv transfer failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } finally {
            if (isset($this->client)) {
                $this->client->close();
            }
        }
    }
}