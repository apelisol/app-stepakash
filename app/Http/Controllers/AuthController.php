<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ForgotPassword;
use App\Models\LoginSession;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordUpdateConfirmationMail;
use App\Mail\WelcomeEmail;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AuthController extends Controller
{
    protected $smsService;
    protected $otpExpiryMinutes = 5;
    protected $resetTokenExpiryMinutes = 30;
    protected $maxOtpAttempts = 5;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Generate a 6-digit OTP
     * 
     * @return string
     */
    protected function generateOTP()
    {
        return strtoupper(Str::random(3) . rand(100, 999));
    }
    
    /**
     * Show the email verification form
     *
     * @return \Illuminate\View\View
     */
    public function showEmailVerificationForm()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to verify your email.');
        }
        
        if (auth()->user()->email_verified_at) {
            return redirect()->route('dashboard')->with('success', 'Your email is already verified.');
        }
        
        return view('auth.verify-email');
    }
    
    /**
     * Show Login Form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Show Registration Form
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Show Forgot Password Form
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Show Verify OTP Form
     */
    public function showVerifyOtpForm($wallet_id = null): View
    {
        return view('auth.verify-otp', ['wallet_id' => $wallet_id]);
    }

    /**
     * Show Reset Password Form
     */
    public function showResetPasswordForm($token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log the login session
            LoginSession::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => '',
                'last_activity' => now()
            ]);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:4',
            'confirmpassword' => 'required|same:password',
            'account_number' => 'sometimes|required',
            'fullname' => 'sometimes|required',
            'deriv_token' => 'sometimes|string',
            'deriv_login_id' => 'sometimes|string',
            'deriv_account_number' => 'sometimes|string',
            'deriv_currency' => 'sometimes|string|size:3',
            'deriv_email' => 'sometimes|email|max:255',
            'email' => 'sometimes|email|max:255',
            'user_id' => 'sometimes|numeric',
            'country' => 'sometimes|string|max:2',
            'landing_company_name' => 'sometimes|string',
            'landing_company_fullname' => 'sometimes|string',
            'is_virtual' => 'sometimes|boolean',
            'is_real_account' => 'sometimes|boolean',
            'all_deriv_accounts' => 'nullable',
            'scopes' => 'nullable',
            'account_list' => 'nullable',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'place_of_birth' => 'nullable|string|max:100',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_state' => 'nullable|string|max:100',
            'address_postcode' => 'nullable|string|max:20',
            'tax_identification_number' => 'nullable|string|max:50',
            'tax_residence' => 'nullable|string|max:100',
            'has_secret_answer' => 'sometimes|boolean',
            'email_consent' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $phone = $this->formatPhoneNumber($request->phone);
            
            // Check if phone already exists
            if (Customer::where('phone', $phone)->exists()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['phone' => 'This phone number is already registered.']);
            }

            // Generate wallet ID starting with SK
            $lastCustomer = Customer::orderBy('id', 'desc')->first();
            $walletId = $lastCustomer ? $this->getNextWallet($lastCustomer->wallet_id) : 'SK0001A';

            // Prepare all customer data from request
            $customerData = [
                'wallet_id' => $walletId,
                'phone' => $phone,
                'password' => Hash::make($request->password),
                'fullname' => $request->fullname ?? trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')),
                'email' => $request->email ?? $request->deriv_email,
                'account_number' => $request->account_number,
                'deriv_token' => $request->deriv_token,
                'deriv_login_id' => $request->deriv_login_id,
                'deriv_account_number' => $request->deriv_account_number,
                'deriv_currency' => $request->deriv_currency,
                'all_deriv_accounts' => $request->all_deriv_accounts ? json_encode($request->all_deriv_accounts) : null,
                'user_id' => $request->user_id ?? $request->deriv_user_id, // Use either user_id or deriv_user_id
                'country' => $request->country,
                'landing_company_name' => $request->landing_company_name,
                'landing_company_fullname' => $request->landing_company_fullname,
                'scopes' => $request->scopes ? (is_array($request->scopes) ? json_encode($request->scopes) : $request->scopes) : null,
                'is_virtual' => $request->is_virtual ?? 0,
                'is_real_account' => $request->is_real_account ?? true,
                'account_list' => $request->account_list ? (is_array($request->account_list) ? json_encode($request->account_list) : $request->account_list) : null,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'place_of_birth' => $request->place_of_birth,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'address_city' => $request->address_city,
                'address_state' => $request->address_state,
                'address_postcode' => $request->address_postcode,
                'tax_identification_number' => $request->tax_identification_number,
                'tax_residence' => $request->tax_residence,
                'has_secret_answer' => $request->has_secret_answer ?? false,
                'email_consent' => $request->email_consent ?? false,
                'deriv_verified' => 1, // Mark as verified since it's coming from OAuth
                'deriv_verification_date' => now(),
                'deriv_last_sync' => now(),
                'deriv_email' => $request->deriv_email,
                'agent' => false
            ];

            // Create customer
            $customer = Customer::create($customerData);
            
            // Log in the user
            Auth::login($customer);
            $request->session()->regenerate();
            
            // Send welcome email
            try {
                Mail::to($customer->email)->send(new WelcomeEmail($customer));
                
                // Send welcome SMS with wallet ID
                $message = "Welcome to StepaKash! Your wallet ID is {$walletId}. You can now log in and start using our services.";
                $this->smsService->sendSms($phone, $message);
                
            } catch (\Exception $e) {
                // Log the error but don't fail the registration
                \Log::error('Failed to send welcome email: ' . $e->getMessage());
                
                return redirect()->route('dashboard')
                    ->with('warning', 'Registration successful! However, we encountered an issue sending your welcome email.');
            }
            
            return redirect()->route('dashboard')
                ->with('success', 'Registration successful! Welcome to StepaKash.');
                    
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }

    /**
     * Send OTP for password reset via email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        
        // Check if the email exists in either email or deriv_email columns
        $customer = Customer::where('deriv_email', $email)
            ->orWhere('email', $email)
            ->first();

        if (!$customer) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'We could not find an account with that email address.');
        }
        
        // Use the email from the customer record to ensure consistency
        $email = $customer->deriv_email ?? $customer->email;

        // Check if there's a recent OTP that hasn't expired yet
        $recentOtp = ForgotPassword::where('email', $email)
            ->where('created_at', '>', now()->subMinutes($this->otpExpiryMinutes))
            ->orderBy('created_at', 'desc')
            ->first();

        if ($recentOtp) {
            $otp = $recentOtp->otp;
            $otpKey = 'otp_' . $customer->wallet_id;
            
            // Update cache with the existing OTP
            Cache::put($otpKey, [
                'otp' => $otp,
                'attempts' => 0,
                'created_at' => $recentOtp->created_at,
                'email' => $email
            ], now()->addMinutes($this->otpExpiryMinutes));
            
            return redirect()->route('password.verify', ['wallet_id' => $customer->wallet_id])
                ->with('status', 'We have resent the verification code to your email. The code is valid for ' . $this->otpExpiryMinutes . ' minutes.');
        }

        // Check if there are too many OTP attempts
        $otpKey = 'otp_' . $customer->wallet_id;
        $existingOtp = Cache::get($otpKey);

        if ($existingOtp && $existingOtp['attempts'] >= $this->maxOtpAttempts) {
            return redirect()->back()
                ->with('error', 'Too many OTP requests. Please try again in 30 minutes.');
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpExpiry = now()->addMinutes($this->otpExpiryMinutes);

        // Store OTP in cache
        Cache::put($otpKey, [
            'otp' => $otp,
            'attempts' => ($existingOtp['attempts'] ?? 0) + 1,
            'created_at' => now(),
            'email' => $email
        ], $otpExpiry);

        // Send OTP via email
        try {
            // Use the email that was used to look up the customer
            $toEmail = $customer->deriv_email ?? $customer->email;
            
            Log::info('Sending OTP email', [
                'to' => $toEmail,
                'wallet_id' => $customer->wallet_id,
                'otp' => $otp
            ]);
            
            Mail::to($toEmail)->send(new PasswordResetMail($customer, $otp));
            
            // Store the wallet ID in the session for verification
            session(['reset_wallet_id' => $customer->wallet_id]);
            
            // Log OTP in database
            ForgotPassword::create([
                'wallet_id' => $customer->wallet_id,
                'email' => $toEmail,
                'otp' => $otp,
                'ip_address' => $request->ip(),
                'method' => 'email',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('OTP email sent successfully', ['to' => $toEmail]);
            
            // Return JSON response for API
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'We have sent a 6-digit verification code to your email. The code is valid for ' . $this->otpExpiryMinutes . ' minutes.',
                    'wallet_id' => $customer->wallet_id
                ]);
            }
            
            // For web requests
            return redirect()->route('password.verify', ['wallet_id' => $customer->wallet_id])
                ->with('status', 'We have sent a 6-digit verification code to your email. The code is valid for ' . $this->otpExpiryMinutes . ' minutes.');
                
        } catch (\Exception $e) {
            $errorMessage = 'Failed to send OTP. Please try again later.';
            Log::error('Failed to send OTP email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'customer_id' => $customer->id ?? null,
                'email' => $toEmail ?? null
            ]);
            
            // Return JSON response for API
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            // For web requests
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'wallet_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid 6-digit OTP.');
        }

        $walletId = $request->wallet_id;
        $otp = $request->otp;
        $otpKey = 'otp_' . $walletId;
        
        // First check the cache
        $storedOtp = Cache::get($otpKey);
        
        // If not in cache or doesn't match, check database
        if (!$storedOtp || $storedOtp['otp'] != $otp) {
            $otpRecord = ForgotPassword::where('wallet_id', $walletId)
                ->where('otp', $otp)
                ->where('created_at', '>', now()->subMinutes($this->otpExpiryMinutes))
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$otpRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid or expired OTP. Please try again.');
            }
            
            // Update cache with the database OTP
            $storedOtp = [
                'otp' => $otpRecord->otp,
                'email' => $otpRecord->email,
                'created_at' => $otpRecord->created_at
            ];
            
            Cache::put($otpKey, $storedOtp, now()->addMinutes($this->otpExpiryMinutes));
        }

        // Generate a password reset token
        $token = Str::random(60);
        Cache::put('password_reset_' . $walletId, [
            'email' => $storedOtp['email'],
            'token' => $token,
            'created_at' => now()
        ], now()->addMinutes($this->resetTokenExpiryMinutes));

        // Clear the OTP from cache
        Cache::forget($otpKey);

        return redirect()->route('password.reset', ['token' => $token, 'email' => $storedOtp['email']])
            ->with('status', 'OTP verified. You can now reset your password.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $walletId = session('reset_wallet_id');
        if (!$walletId) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid password reset request.');
        }

        $resetData = Cache::get('password_reset_' . $walletId);
        if (!$resetData || $resetData['token'] !== $request->token) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired password reset token.');
        }

        // Update the user's password
        $customer = Customer::where('wallet_id', $walletId)->first();
        if (!$customer) {
            return redirect()->route('password.request')
                ->with('error', 'User not found.');
        }

        $customer->password = Hash::make($request->password);
        $customer->save();

        // Clear the reset token
        Cache::forget('password_reset_' . $walletId);
        $request->session()->forget('reset_wallet_id');

        // Send password update confirmation
        try {
            Mail::to($customer->deriv_email)
                ->send(new PasswordUpdateConfirmationMail($customer));
                
            // Also send SMS notification
            $message = "Your StepaKash password has been successfully updated. If you didn't make this change, please contact support immediately.";
            $this->smsService->sendSms($customer->phone, $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to send password update confirmation: ' . $e->getMessage());
            // Continue even if email/SMS fails
        }

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. You can now log in with your new password.');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to log out',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate next wallet ID
     */
    private function getNextWallet($currentReceipt)
    {
        // Remove 'SK' prefix if it exists for processing
        $currentReceipt = str_starts_with($currentReceipt, 'SK') ? substr($currentReceipt, 2) : $currentReceipt;
        
        preg_match('/([A-Z]+)(\d+)([A-Z]*)/', $currentReceipt, $matches);
        $letters = $matches[1];
        $digits = intval($matches[2]);
        $extraLetter = $matches[3] ?? '';

        $maxDigits = 4;
        $maxLetters = 2;

        // Increment digits
        $nextDigits = $digits + 1;
        $nextLetters = $letters;
        $nextExtraLetter = $extraLetter;

        // Handle digit rollover
        if ($nextDigits > pow(10, $maxDigits) - 1) {
            $nextDigits = 1;
            
            // Increment letters (A-Z)
            $letterPos = strlen($nextLetters) - 1;
            while ($letterPos >= 0) {
                $nextChar = $nextLetters[$letterPos];
                if ($nextChar === 'Z') {
                    $nextLetters[$letterPos] = 'A';
                    $letterPos--;
                } else {
                    $nextLetters[$letterPos] = chr(ord($nextChar) + 1);
                    break;
                }
            }
            
            // If we've gone through all letters, add an extra letter
            if ($letterPos < 0) {
                if (empty($nextExtraLetter)) {
                    $nextExtraLetter = 'A';
                } else if ($nextExtraLetter === 'Z') {
                    $nextExtraLetter = 'A';
                    $nextLetters = str_repeat('A', $maxLetters);
                } else {
                    $nextExtraLetter = chr(ord($nextExtraLetter) + 1);
                }
            }
        }

        $nextDigitsStr = str_pad($nextDigits, $maxDigits, '0', STR_PAD_LEFT);

        // Always return with SK prefix
        return 'SK' . $nextLetters . $nextDigitsStr . $nextExtraLetter;
    }

    /**
     * Format phone number to Kenyan standard
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If it starts with 0, replace with +254
        if (strpos($phone, '0') === 0) {
            $phone = '254' . substr($phone, 1);
        }
        // If it starts with 7 or 1, add 254
        elseif (preg_match('/^[17]/', $phone)) {
            $phone = '254' . $phone;
        }
        // If it starts with 254, leave as is
        
        return $phone;
    }
}
