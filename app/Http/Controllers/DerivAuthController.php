<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

        // Store the accounts in the session for the loading page
        session(['deriv_auth_accounts' => $accounts]);
        session()->forget('oauth_state');

        // Show a loading/authorizing page that will make the authorize call
        return view('auth.deriv-authorizing', [
            'accounts' => $accounts,
            'primary_account' => $accounts[0] // Default to first account
        ]);
    }

    /**
     * Process the authorization with Deriv API
     */
    public function authorizeAccount(Request $request)
    {
        $accountNumber = $request->input('account_number');
        $accounts = session('deriv_auth_accounts', []);
        
        // Find the selected account
        $selectedAccount = collect($accounts)->firstWhere('account_number', $accountNumber);
        
        if (!$selectedAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Selected account not found'
            ], 404);
        }

        try {
            // Make the authorize call to Deriv API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $selectedAccount['token']
            ])->post('https://api.deriv.com/api/v1/', [
                'authorize' => $selectedAccount['token'],
                'req_id' => (int) round(microtime(true) * 1000) // Unique request ID
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to authorize with Deriv API');
            }

            $authData = $response->json();
            \Log::info('Deriv API Authorize Response:', $authData);
            
            if (!isset($authData['authorize'])) {
                $errorMsg = $authData['error']['message'] ?? 'Invalid response from Deriv API';
                \Log::error('Deriv API Error:', [
                    'error' => $errorMsg,
                    'full_response' => $authData
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authorize with Deriv: ' . $errorMsg
                ], 400);
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
    private function extractAccountsFromRequest(Request $request)
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
