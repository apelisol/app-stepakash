<?php

class MpesaBalanceChecker
{
    private $mpesa_consumer_key = 'wC9zwOZCu2XQYAqK7xnH4eYQHfYxOZxuVZARqoONzjVUAljA';
    private $mpesa_consumer_secret = 'rGDcF6VKvrGE6e52gAAve9UWXBnzs1iDwUPaV2kVICLzMHiDtU5W87xJAzNg6KeA';
    private $InitiatorName = 'STEVEWEB';
    private $password = '..ken6847musyimI.';
    private $PartyA = '4168325'; // Your shortcode
    private $ResultURL = 'https://api.stepakash.com/index.php/balance_result';
    private $QueueTimeOutURL = 'https://api.stepakash.com/index.php/balance_timeout';

    /**
     * Get M-Pesa access token
     */
    private function getAccessToken()
    {
        $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $credentials = base64_encode($this->mpesa_consumer_key . ':' . $this->mpesa_consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($curl_response, true);

        if (isset($response['access_token'])) {
            return $response['access_token'];
        }

        throw new Exception('Failed to get access token: ' . $curl_response);
    }

    /**
     * Generate security credential
     */
    private function generateSecurityCredential()
    {
        $publicKey_path = 'certicates/mpesa_cert.cer';

        if (!file_exists($publicKey_path)) {
            throw new Exception('Certificate file not found: ' . $publicKey_path);
        }

        $fp = fopen($publicKey_path, "r");
        $publicKey = fread($fp, 8192);
        fclose($fp);

        $plaintext = $this->password;
        openssl_public_encrypt($plaintext, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);

        return base64_encode($encrypted);
    }

    /**
     * Check M-Pesa float balance
     */
    public function checkBalance()
    {
        try {
            // Get access token
            $access_token = $this->getAccessToken();
            echo "✓ Access token obtained successfully\n";

            // Generate security credential
            $security_credential = $this->generateSecurityCredential();
            echo "✓ Security credential generated\n";

            // Prepare balance inquiry request
            $url = 'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query';

            $curl_post_data = array(
                'Initiator' => $this->InitiatorName,
                'SecurityCredential' => $security_credential,
                'CommandID' => 'AccountBalance',
                'PartyA' => $this->PartyA,
                'IdentifierType' => '4', // 4 for organization shortcode
                'Remarks' => 'Balance Inquiry',
                'QueueTimeOutURL' => $this->QueueTimeOutURL,
                'ResultURL' => $this->ResultURL
            );

            $data_string = json_encode($curl_post_data);

            // Make the API call
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $curl_response = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            echo "HTTP Response Code: " . $http_code . "\n";
            echo "Raw Response: " . $curl_response . "\n\n";

            $responseArray = json_decode($curl_response, true);

            if ($responseArray) {
                echo "=== M-PESA BALANCE INQUIRY RESPONSE ===\n";
                echo "Response Code: " . ($responseArray['ResponseCode'] ?? 'N/A') . "\n";
                echo "Response Description: " . ($responseArray['ResponseDescription'] ?? 'N/A') . "\n";

                if (isset($responseArray['ConversationID'])) {
                    echo "Conversation ID: " . $responseArray['ConversationID'] . "\n";
                }

                if (isset($responseArray['OriginatorConversationID'])) {
                    echo "Originator Conversation ID: " . $responseArray['OriginatorConversationID'] . "\n";
                }

                echo "=====================================\n\n";

                // Check if request was successful
                if (isset($responseArray['ResponseCode']) && $responseArray['ResponseCode'] == '0') {
                    echo "✓ Balance inquiry request submitted successfully!\n";
                    echo "📱 Check your callback URLs for the actual balance details.\n";
                    echo "📋 The balance will be sent to: " . $this->ResultURL . "\n";
                } else {
                    echo "❌ Balance inquiry failed!\n";
                    echo "Error: " . ($responseArray['ResponseDescription'] ?? 'Unknown error') . "\n";
                }

                return $responseArray;
            } else {
                echo "❌ Failed to decode API response\n";
                return false;
            }
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Simple balance check with minimal output
     */
    public function quickBalanceCheck()
    {
        $result = $this->checkBalance();

        if ($result && isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
            return [
                'success' => true,
                'conversation_id' => $result['ConversationID'] ?? null,
                'message' => 'Balance inquiry submitted successfully'
            ];
        }

        return [
            'success' => false,
            'message' => $result['ResponseDescription'] ?? 'Balance inquiry failed'
        ];
    }
}

// Usage example - Uncomment to test

echo "🚀 Starting M-Pesa Balance Check...\n";
echo "=====================================\n\n";

$balanceChecker = new MpesaBalanceChecker();
$result = $balanceChecker->checkBalance();

echo "\n🏁 Balance check completed!\n";

// Alternative quick check
echo "\n--- Quick Balance Check ---\n";
$quickResult = $balanceChecker->quickBalanceCheck();
echo "Success: " . ($quickResult['success'] ? 'Yes' : 'No') . "\n";
echo "Message: " . $quickResult['message'] . "\n";
