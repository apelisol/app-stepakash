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
            return redirect()->route('auth.deriv')->with('msg', 'Invalid state parameter');
        }

        $accounts = $this->extractAccountsFromRequest($request);

        if (empty($accounts)) {
            return redirect()->route('auth.deriv')->with('msg', 'No eligible USD Deriv accounts found (demo accounts not allowed)');
        }

        $primaryAccount = $accounts[0];
        $additionalInfo = $this->getDerivAccountInfo($primaryAccount['token']);

        $derivData = [
            // Account information
            'deriv_token' => $primaryAccount['token'],
            'deriv_login_id' => $primaryAccount['account_number'],
            'deriv_account_number' => $primaryAccount['account_number'],
            'deriv_currency' => $primaryAccount['currency'],
            'all_deriv_accounts' => $accounts,
            'is_real_account' => true,
            
            // User information from authorize endpoint
            'user_id' => $additionalInfo['user_id'] ?? null,
            'email' => $additionalInfo['email'] ?? '',
            'fullname' => $additionalInfo['fullname'] ?? '',
            'country' => $additionalInfo['country'] ?? '',
            'landing_company_name' => $additionalInfo['landing_company_name'] ?? '',
            'landing_company_fullname' => $additionalInfo['landing_company_fullname'] ?? '',
            'scopes' => $additionalInfo['scopes'] ?? [],
            'is_virtual' => $additionalInfo['is_virtual'] ?? false,
            'account_list' => $additionalInfo['account_list'] ?? [],
            
            // Additional user details from settings
            'first_name' => $additionalInfo['first_name'] ?? '',
            'last_name' => $additionalInfo['last_name'] ?? '',
            'date_of_birth' => $additionalInfo['date_of_birth'] ?? '',
            'place_of_birth' => $additionalInfo['place_of_birth'] ?? '',
            'address' => [
                'line_1' => $additionalInfo['address_line_1'] ?? '',
                'line_2' => $additionalInfo['address_line_2'] ?? '',
                'city' => $additionalInfo['address_city'] ?? '',
                'state' => $additionalInfo['address_state'] ?? '',
                'postcode' => $additionalInfo['address_postcode'] ?? ''
            ],
            'phone' => $additionalInfo['phone'] ?? '',
            'has_secret_answer' => $additionalInfo['has_secret_answer'] ?? false,
            'email_consent' => $additionalInfo['email_consent'] ?? 0,
            'tax_information' => [
                'identification_number' => $additionalInfo['tax_identification_number'] ?? '',
                'residence' => $additionalInfo['tax_residence'] ?? ''
            ]
        ];

        session(['deriv_data' => $derivData]);
        session()->forget('oauth_state');

        return redirect()->route('register');
    }

    /**
     * Get Deriv account information including comprehensive user details
     */
    private function getDerivAccountInfo($token)
    {
        try {
            // First, get account authorization details
            $authResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post('https://api.deriv.com/api/v1/', [
                'authorize' => $token,
                'req_id' => (int) round(microtime(true) * 1000) // Unique request ID
            ]);

            // Then get account settings
            $settingsResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post('https://api.deriv.com/api/v1/', [
                'get_settings' => 1,
                'req_id' => (int) round(microtime(true) * 1000)
            ]);

            $userData = [];
            
            // Process authorization response
            if ($authResponse->successful()) {
                $authData = $authResponse->json();
                if (isset($authData['authorize'])) {
                    $auth = $authData['authorize'];
                    $userData = [
                        'email' => $auth['email'] ?? '',
                        'fullname' => $auth['fullname'] ?? '',
                        'country' => $auth['country'] ?? '',
                        'currency' => $auth['currency'] ?? 'USD',
                        'user_id' => $auth['user_id'] ?? null,
                        'loginid' => $auth['loginid'] ?? '',
                        'landing_company_name' => $auth['landing_company_name'] ?? '',
                        'landing_company_fullname' => $auth['landing_company_fullname'] ?? '',
                        'scopes' => $auth['scopes'] ?? [],
                        'is_virtual' => $auth['is_virtual'] ?? false,
                        'account_list' => $auth['account_list'] ?? []
                    ];
                }
            }

            // Process settings response to get additional user details
            if ($settingsResponse->successful()) {
                $settingsData = $settingsResponse->json();
                if (isset($settingsData['get_settings'])) {
                    $settings = $settingsData['get_settings'];
                    $userData = array_merge($userData, [
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
                    ]);
                }
            }

            return $userData;
            
        } catch (\Exception $e) {
            Log::error('Failed to get Deriv account info: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return [];
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
