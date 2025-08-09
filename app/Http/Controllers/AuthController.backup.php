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
     * Verify the email using OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to verify your email.');
        }
        
        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('success', 'Your email is already verified.');
        }
        
        // Check if OTP matches and is not expired
        if ($user->otp === $request->otp && $user->otp_expires_at > now()) {
            $user->update([
                'email_verified_at' => now(),
                'otp' => null,
                'otp_expires_at' => null,
            ]);
            
            return redirect()->route('dashboard')
                ->with('success', 'Your email has been verified successfully!');
        }
        
        return back()
            ->withInput()
            ->withErrors(['otp' => 'The verification code is invalid or has expired.']);
    }
    
    /**
     * Resend the verification OTP
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationOtp(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        
        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 400);
        }
        
        // Generate new OTP
        $otp = $this->generateOTP();
        $otpExpiresAt = now()->addMinutes(30);
        
        // Update user with new OTP
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => $otpExpiresAt,
        ]);
        
        // Send the new OTP via email
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user, $otp));
            
            // Also send via SMS if phone number exists
            if ($user->phone) {
                $message = "Your new verification code is: {$otp}. It will expire in 30 minutes.";
                $this->smsService->sendSms($user->phone, $message);
            }
            
            return back()->with('status', 'A new verification code has been sent to your email.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification OTP: ' . $e->getMessage());
            return back()->with('error', 'Failed to send verification code. Please try again later.');
        }
    }

    // Show Login Form
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // Show Registration Form
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    // Show Forgot Password Form
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    // Show Verify OTP Form
    public function showVerifyOtpForm($wallet_id = null): View
    {
        return view('auth.verify-otp', ['wallet_id' => $wallet_id]);
    }

    // Show Reset Password Form
    public function showResetPasswordForm($token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
            'ip_address' => 'required|ip'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $phone = $this->formatPhoneNumber($request->phone);
        $password = trim(str_replace(' ', '', $request->password));

        // Attempt to authenticate the customer
        if (Auth::attempt(['phone' => $phone, 'password' => $password], $request->filled('remember'))) {
            $customer = Auth::user();
            $sessionId = Str::uuid()->toString();
            
            // Record login session
            LoginSession::create([
                'customer_id' => $customer->id,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'login_at' => now()
            ]);

            // Update last login time
            $customer->last_login_at = now();
            $customer->save();

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return redirect()->back()
            ->withInput($request->except('password'))
            ->withErrors([
                'login' => 'Invalid phone number or password.',
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
            'user_id' => 'sometimes|numeric',
            'country' => 'sometimes|string|max:2',
            'landing_company_name' => 'sometimes|string',
            'landing_company_fullname' => 'sometimes|string',
            'is_virtual' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'confirmpassword'));
        }

        try {
            // Format phone number
            $phone = $this->formatPhoneNumber($request->phone);
            if (!$phone) {
                return redirect()->back()
                    ->withInput($request->except('password', 'confirmpassword'))
                    ->withErrors(['phone' => 'Invalid phone number format. Please use a valid Kenyan phone number.']);
            }

            // Check if phone already exists
            if (Customer::where('phone', $phone)->exists()) {
                return redirect()->back()
                    ->withInput($request->except('password', 'confirmpassword'))
                    ->withErrors(['phone' => 'This phone number is already registered.']);
            }

            // Generate wallet ID starting with SK
            $lastCustomer = Customer::orderBy('id', 'desc')->first();
            $walletId = $lastCustomer ? $this->getNextWallet($lastCustomer->wallet_id) : 'SK0001A';

            // Create customer with essential Deriv CR account data
            $customerData = [
                'phone' => $phone,
                'password' => Hash::make($request->password),
                'wallet_id' => $walletId,
                'account_number' => $request->account_number,
                'fullname' => $request->fullname,
                'email' => $request->deriv_email, // Map Deriv email to main email
                'country' => $request->country, // Store country from Deriv
                // Deriv account fields
                'deriv_account' => 1, // Always true for Deriv signups
                'deriv_token' => $request->deriv_token,
                'deriv_email' => $request->deriv_email,
                'deriv_login_id' => $request->deriv_login_id,
                'deriv_account_number' => $request->deriv_account_number,
                'deriv_currency' => $request->deriv_currency,
                'user_id' => $request->user_id,
                'landing_company_name' => $request->landing_company_name,
                'landing_company_fullname' => $request->landing_company_fullname,
                // Set verification status
                'deriv_verified' => 1,
                'deriv_verification_date' => now(),
                'deriv_last_sync' => now()
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
                ->withInput($request->except('password', 'confirmpassword'))
                ->with('error', 'Failed to create account. Please try again.');
        }
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
            // Invalidate the session
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
     * Send OTP for password reset via email or SMS
     */
    public function sendOtp(Request $request)
    {
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
 * Send OTP for password reset via email
 */
public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:customers,deriv_email',
    ]);

    $email = $request->email;
    $customer = Customer::where('deriv_email', $email)->first();

    if (!$customer) {
        return redirect()->back()
            ->with('error', 'We could not find an account with that email address.');
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
        Mail::to($customer->deriv_email)->send(new PasswordResetMail($customer, $otp));
        
        // Store the wallet ID in the session for verification
        session(['reset_wallet_id' => $customer->wallet_id]);
        
        // Log OTP in database
        ForgotPassword::create([
            'wallet_id' => $customer->wallet_id,
            'phone' => $customer->phone,
            'email' => $email,
            'otp' => $otp,
            'method' => 'email',
            'ip_address' => $request->ip()
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

        $customer = Customer::where('wallet_id', $walletId)->first();

        if (!$customer) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Account not found. Please try again.');
        }

        $otpKey = 'otp_' . $walletId;
        $storedOtp = Cache::get($otpKey);

        // Check if OTP exists in cache
        if (!$storedOtp) {
            // Fallback to database if not in cache
            $otpRecord = ForgotPassword::where('wallet_id', $walletId)
                ->where('otp', $otp)
                ->where('created_at', '>', now()->subMinutes($this->otpExpiryMinutes))
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$otpRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid or expired OTP. Please request a new one.');
            }
            
            // Use OTP from database if found
            $storedOtp = [
                'otp' => $otpRecord->otp,
                'attempts' => 0,
                'email' => $otpRecord->email
            ];
            
            // Update cache with the database OTP
            Cache::put($otpKey, $storedOtp, now()->addMinutes($this->otpExpiryMinutes));
        } 
        
        // Check if OTP matches
        if ($storedOtp['otp'] != $otp) {
            // Increment failed attempts
            $storedOtp['attempts'] = ($storedOtp['attempts'] ?? 0) + 1;
            $attemptsRemaining = $this->maxOtpAttempts - $storedOtp['attempts'];
            
            Cache::put($otpKey, $storedOtp, now()->addMinutes($this->otpExpiryMinutes));

            if ($storedOtp['attempts'] >= $this->maxOtpAttempts) {
                Cache::forget($otpKey);
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Too many failed attempts. Please request a new OTP.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', "Invalid OTP. {$attemptsRemaining} attempts remaining.");
        }

        // Generate password reset token
        $resetToken = Str::random(60);
        $resetTokenKey = 'reset_token_' . $walletId;
        
        // Store reset token in cache for 30 minutes
        Cache::put($resetTokenKey, [
            'token' => $resetToken,
            'email' => $storedOtp['email']
        ], now()->addMinutes($this->resetTokenExpiryMinutes));

        // Clear OTP from cache after successful verification
        Cache::forget($otpKey);
        
        // Store reset token in session for the next step
        session(['reset_token' => $resetToken]);
        
        // Redirect to password reset page with success message
        return redirect()->route('password.reset', ['token' => $resetToken])
            ->with('status', 'OTP verified successfully. You can now reset your password.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ], [
            'password.required' => 'Please enter a new password',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Passwords do not match',
            'password_confirmation.required' => 'Please confirm your password',
            'token.required' => 'Invalid or expired reset link'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $token = $request->token;
        $resetTokenKey = 'reset_token_' . $token;
        $storedToken = Cache::get($resetTokenKey);

        if (!$storedToken) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired reset link. Please request a new one.');
        }

        // Find customer by email from the token data
        $customer = Customer::where('deriv_email', $storedToken['email'])->first();
        if (!$customer) {
            return redirect()->route('password.request')
                ->with('error', 'Account not found. Please try again.');
        }

        try {
            // Update password
            $customer->password = Hash::make($request->password);
            $customer->save();

            // Clear the reset token
            Cache::forget($resetTokenKey);
            
            // Clear any session data
            $request->session()->forget('reset_token');
            $request->session()->forget('reset_wallet_id');

            // Send confirmation email/SMS
            $this->sendPasswordUpdateConfirmation($customer);

            // Log the user in automatically
            Auth::login($customer);

            return redirect()->route('dashboard')
                ->with('success', 'Your password has been updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Password reset failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update password. Please try again.')
                ->withInput();
        }
        // This code is now handled in the try-catch block above
    }

    /**
     * Send password update confirmation via email and SMS
     */
    protected function sendPasswordUpdateConfirmation(Customer $customer)
    {
        try {
            // Send email confirmation if email exists
            if ($customer->deriv_email) {
                Mail::to($customer->deriv_email)
                    ->send(new PasswordUpdateConfirmationMail($customer));
            }

            // Send SMS confirmation
            $smsMessage = view('sms.password_update_confirmation', [
                'customer' => $customer
            ])->render();

            $this->smsService->sendSms($customer->phone, $smsMessage);
        } catch (\Exception $e) {
            Log::error('Failed to send password update confirmation', [
                'wallet_id' => $customer->wallet_id,
                'error' => $e->getMessage()
            ]);

            // You might want to queue a retry here or notify admin
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

        if (!empty($extraLetter)) {
            $nextExtraLetter = chr(ord($extraLetter) + 1);

            if ($nextExtraLetter > 'Z') {
                $nextExtraLetter = 'A';
                $nextDigits = $digits + 1;
            } else {
                $nextDigits = $digits;
            }
        } else {
            $nextExtraLetter = 'A';
            $nextDigits = $digits + 1;
        }

        if ($nextDigits > str_repeat('9', $maxDigits)) {
            $lettersArray = str_split($letters);
            $lastIndex = count($lettersArray) - 1;
            $lettersArray[$lastIndex] = chr(ord($lettersArray[$lastIndex]) + 1);
            $nextLetters = implode('', $lettersArray);

            if (strlen($nextLetters) > $maxLetters) {
                $nextLetters = 'A';
                $nextDigits = 1;
            }
        } else {
            $nextLetters = $letters;
        }

        $nextDigitsStr = str_pad($nextDigits, $maxDigits, '0', STR_PAD_LEFT);

        // Always return with SK prefix
        return 'SK' . $nextLetters . $nextDigitsStr . $nextExtraLetter;
    }

    /**
     * Format phone number to Kenyan standard
     */
    private function formatPhoneNumber($phone)
    {
        $phone = str_replace(' ', '', $phone);
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($digits) === 9 && $digits[0] === '7') {
            return '+254' . $digits;
        } elseif (strlen($digits) === 10 && substr($digits, 0, 2) === '07') {
            return '+254' . substr($digits, 1);
        } elseif (strlen($digits) === 12 && substr($digits, 0, 3) === '254') {
            return '+' . $digits;
        } elseif (strlen($phone) === 13 && substr($phone, 0, 4) === '+254') {
            return $phone;
        }

        return false;
    }

    // API Login
    public function apiLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('phone', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token
        ]);
    }

    // API Logout
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    // API Get Authenticated User
    public function getAuthenticatedUser(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()
        ]);
    }
}
