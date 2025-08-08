<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DerivAuthController extends Controller
{
    private $appId = '76420';
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
            'deriv_token' => $primaryAccount['token'],
            'deriv_login_id' => $primaryAccount['account_number'],
            'deriv_account_number' => $primaryAccount['account_number'],
            'deriv_currency' => $primaryAccount['currency'],
            'all_deriv_accounts' => $accounts,
            'deriv_email' => $additionalInfo['email'] ?? '',
            'fullname' => $additionalInfo['fullname'] ?? '',
            'is_real_account' => true
        ];

        session(['deriv_data' => $derivData]);
        session()->forget('oauth_state');

        return redirect()->route('register');
    }

    /**
     * Get Deriv account information
     */
    private function getDerivAccountInfo($token)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ])->post('https://api.deriv.com/api/v1/', [
                'get_settings' => 1,
                'req_id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['get_settings'])) {
                    return [
                        'email' => $data['get_settings']['email'] ?? '',
                        'fullname' => trim(($data['get_settings']['first_name'] ?? '') . ' ' . ($data['get_settings']['last_name'] ?? '')),
                        'country' => $data['get_settings']['country'] ?? '',
                        'currency' => $data['get_settings']['currency'] ?? 'USD'
                    ];
                }
            }
            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get Deriv account info: ' . $e->getMessage());
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
