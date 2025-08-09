<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use WebSocket\Client as WebSocketClient;
use Illuminate\Support\Str;

class DerivAuthController extends Controller
{
    private $appId = '92272';
    private $scope = 'read,trade,trading_information,payments';

    public function showDerivAuth()
    {
        return view('auth.deriv-auth');
    }

    public function initiateOAuth()
    {
        $state = Str::random(32);
        session(['oauth_state' => $state]);

        $redirectUri = route('auth.deriv.callback');

        $oauthUrl = "https://oauth.deriv.com/oauth2/authorize?" . http_build_query([
            'app_id' => $this->appId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $this->scope,
            'state' => $state
        ]);

        return response()->json([
            'status' => 'success',
            'oauth_url' => $oauthUrl
        ]);
    }

    public function handleCallback(Request $request)
    {
        $receivedState = $request->state;
        $storedState = session('oauth_state');

        if ($receivedState !== $storedState) {
            return redirect()->route('auth.deriv')->with('error', 'Invalid state parameter');
        }

        $accounts = $this->extractAccountsFromRequest($request);

        if (empty($accounts)) {
            return redirect()->route('auth.deriv')->with('error', 'No eligible Deriv accounts found');
        }

        session(['deriv_auth_accounts' => $accounts]);
        session()->forget('oauth_state');

        return view('auth.deriv-authorizing', [
            'accounts' => $accounts,
            'primary_account' => $accounts[0] 
        ]);
    }

    /**
     * Process the authorization with Deriv API
     */
    public function authorizeAccount(Request $request)
    {
        try {
            $accountNumber = $request->input('account_number');
            $accounts = session('deriv_auth_accounts', []);
            
            // Find the selected account
            $selectedAccount = null;
            foreach ($accounts as $account) {
                if ($account['account_number'] === $accountNumber) {
                    $selectedAccount = $account;
                    break;
                }
            }

            if (!$selectedAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found in session. Please try again.'
                ], 400);
            }

            // Initialize WebSocket connection to Deriv API
            $wsUrl = 'wss://ws.derivws.com/websockets/v3?app_id=92272';
            \Log::info('Connecting to WebSocket:', ['url' => $wsUrl]);
            
            try {
                $ws = new WebSocketClient($wsUrl, [
                    'timeout' => 15, // 15 seconds timeout
                    'headers' => [
                        'Origin' => config('app.url'),
                        'User-Agent' => 'Stepakash/1.0',
                    ]
                ]);
                
                // Set a custom error handler for WebSocket
                set_error_handler(function($errno, $errstr) use (&$ws) {
                    \Log::error('WebSocket Error:', [
                        'errno' => $errno,
                        'error' => $errstr
                    ]);
                    
                    if (isset($ws) && $ws->isConnected()) {
                        try {
                            $ws->close();
                        } catch (\Exception $e) {
                            \Log::error('Error closing WebSocket:', ['error' => $e->getMessage()]);
                        }
                    }
                    
                    throw new \Exception('WebSocket connection error: ' . $errstr);
                });
                
                // Authorize with the token
                $authRequest = [
                    'authorize' => $selectedAccount['token'],
                    'req_id' => (int) round(microtime(true) * 1000)
                ];
                
                \Log::info('Sending WebSocket auth request:', $authRequest);
                $ws->send(json_encode($authRequest));
                
                // Set a timeout for the response
                $authResponse = null;
                $startTime = time();
                $timeout = 10; // 10 seconds timeout
                
                while (true) {
                    if ((time() - $startTime) > $timeout) {
                        throw new \Exception('WebSocket authorization timeout');
                    }
                    
                    try {
                        $response = $ws->receive();
                        $authResponse = json_decode($response, true);
                        \Log::info('Received WebSocket auth response:', $authResponse);
                        
                        if ($authResponse !== null) {
                            break;
                        }
                    } catch (\WebSocket\TimeoutException $e) {
                        // Continue waiting
                        continue;
                    } catch (\Exception $e) {
                        \Log::error('Error receiving WebSocket response:', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                    
                    usleep(100000); // 100ms delay between checks
                }
                
                if (!isset($authResponse['authorize'])) {
                    $errorMsg = $authResponse['error']['message'] ?? 'Invalid response from Deriv WebSocket';
                    throw new \Exception($errorMsg);
                }
                
                $auth = $authResponse['authorize'];
                
                // Get account settings
                $settingsRequest = [
                    'get_settings' => 1,
                    'req_id' => (int) round(microtime(true) * 1000)
                ];
                
                $ws->send(json_encode($settingsRequest));
                $settingsResponse = json_decode($ws->receive(), true);
                \Log::info('Deriv WebSocket Settings Response:', $settingsResponse);
                
                $settings = $settingsResponse['get_settings'] ?? [];
                
                // Get account status
                $statusRequest = [
                    'get_account_status' => 1,
                    'req_id' => (int) round(microtime(true) * 1000) + 1
                ];
                
                $ws->send(json_encode($statusRequest));
                $statusResponse = json_decode($ws->receive(), true);
                \Log::info('Deriv WebSocket Status Response:', $statusResponse);
                
                $accountStatus = $statusResponse['get_account_status'] ?? [];
                
                // Close the WebSocket connection
                $ws->close();
                
                // Process the successful response
                $authData = [
                    'authorize' => $auth,
                    'get_settings' => $settings,
                    'get_account_status' => $accountStatus
                ];
                
                if (!isset($authData['authorize'])) {
                    throw new \Exception('Failed to authorize with Deriv API');
                }

                $auth = $authData['authorize'];
                
                // Get additional account settings
                $settings = [];
                try {
                    $settingsResponse = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $selectedAccount['token']
                    ])->post('https://api.deriv.com/api/v1/', [
                        'get_settings' => 1,
                        'req_id' => (int) round(microtime(true) * 1000)
                    ]);

                    if ($settingsResponse->successful()) {
                        $settingsData = $settingsResponse->json();
                        if (isset($settingsData['error'])) {
                            \Log::warning('Failed to get account settings:', [
                                'error' => $settingsData['error'],
                                'account' => $selectedAccount['account_number']
                            ]);
                        } else if (isset($settingsData['get_settings'])) {
                            $settings = $settingsData['get_settings'];
                        }
                    } else {
                        \Log::warning('Failed to fetch account settings', [
                            'status' => $settingsResponse->status(),
                            'response' => $settingsResponse->body()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error fetching account settings:', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Prepare the deriv data for session
                $derivData = [
                    // Account information
                    'deriv_token' => $selectedAccount['token'],
                    'deriv_login_id' => $selectedAccount['account_number'],
                    'deriv_account_number' => $selectedAccount['account_number'],
                    'deriv_currency' => $selectedAccount['currency'],
                    'all_deriv_accounts' => $accounts,
                    'is_real_account' => true,
                    
                    // User information from authorize endpoint
                    'user_id' => $auth['user_id'] ?? null,
                    'email' => $auth['email'] ?? '',
                    'fullname' => $auth['fullname'] ?? '',
                    'country' => $auth['country'] ?? '',
                    'landing_company_name' => $auth['landing_company_name'] ?? '',
                    'landing_company_fullname' => $auth['landing_company_fullname'] ?? '',
                    'scopes' => $auth['scopes'] ?? [],
                    'is_virtual' => $auth['is_virtual'] ?? false,
                    'account_list' => $auth['account_list'] ?? [],
                    
                    // Additional user details from settings
                    'first_name' => $settings['first_name'] ?? '',
                    'last_name' => $settings['last_name'] ?? '',
                    'date_of_birth' => $settings['date_of_birth'] ?? '',
                    'place_of_birth' => $settings['place_of_birth'] ?? '',
                    'address_line_1' => $settings['address_line_1'] ?? '',
                    'address_line_2' => $settings['address_line_2'] ?? '',
                    'address_city' => $settings['address_city'] ?? '',
                    'address_state' => $settings['address_state'] ?? '',
                    'address_postcode' => $settings['address_postcode'] ?? '',
                    'phone' => $settings['phone'] ?? '',
                    'has_secret_answer' => $settings['has_secret_answer'] ?? false,
                    'email_consent' => $settings['email_consent'] ?? 0,
                    'tax_identification_number' => $settings['tax_identification_number'] ?? '',
                    'tax_residence' => $settings['tax_residence'] ?? ''
                ];

                // Store in session for the registration process
                session(['deriv_data' => $derivData]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('register')
                ]);

            } catch (\Exception $e) {
                Log::error('WebSocket Error: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to Deriv WebSocket: ' . $e->getMessage()
                ], 500);
            } finally {
                // Restore the default error handler
                restore_error_handler();
            }
            
        } catch (\Exception $e) {
            Log::error('Error authorizing Deriv account: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to authorize with Deriv: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract accounts from request parameters
     */
    private function extractAccountsFromRequest(Request $request): array
    {
        $accounts = [];
        $i = 1;

        while ($request->has("acct$i") && $request->has("token$i")) {
            $accountNumber = $request->get("acct$i");
            $currency = $request->get("cur$i", 'USD');

            if (strtoupper($currency) === 'USD' && strpos($accountNumber, 'CR') === 0) {
                $accounts[] = [
                    'account_number' => $accountNumber,
                    'token' => $request->get("token$i"),
                    'currency' => $currency,
                    'is_real' => true
                ];
            }
            $i++;
        }

        return $accounts;
    }
}