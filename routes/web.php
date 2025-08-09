<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DerivAuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\MpesaController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/


// Home Route
Route::get('/', function () {
    return view('welcome');
});

// Test email route
Route::get('/test-email', function () {
    try {
        $otp = rand(100000, 999999);
        
        // Log the OTP for testing purposes
        \Illuminate\Support\Facades\Log::info('Test OTP generated', ['otp' => $otp]);
        
        // In a real scenario, you would send the email with the OTP
        // For testing, we'll just log it
        return response()->json([
            'success' => true,
            'message' => 'We have sent a 6-digit verification code to your email. The code is valid for 30 minutes.',
            'otp' => $otp // Only for testing, remove in production
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again later.',
            'error' => $e->getMessage()
        ], 500);
    }
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Standard Authentication
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Registration routes
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('signup');
    
    // OTP Verification for Registration
    Route::get('/verify-email', [AuthController::class, 'showEmailVerificationForm'])->name('verify.email');
    Route::post('/verify-email', [AuthController::class, 'verifyEmailOtp'])->name('verify.email.submit');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationOtp'])->name('verification.resend');

    // Password Reset Flow
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp/{wallet_id?}', [AuthController::class, 'showVerifyOtpForm'])->name('password.verify');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.submit');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

    // Deriv OAuth Authentication
    Route::prefix('auth/deriv')->group(function () {
        Route::get('/', [DerivAuthController::class, 'showDerivAuth'])->name('auth.deriv');
        Route::get('/oauth', [DerivAuthController::class, 'initiateOAuth'])->name('auth.deriv.oauth');
        Route::get('/callback', [DerivAuthController::class, 'handleCallback'])->name('auth.deriv.callback');
        Route::post('/authorize', [DerivAuthController::class, 'authorizeAccount'])->name('auth.deriv.authorize');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Authentication Actions
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Wallet Routes
    Route::prefix('wallet')->name('wallet.')->group(function () {

        // Main Wallet Pages
        Route::get('/', [WalletController::class, 'dashboard'])->name('dashboard');
        Route::get('/balance', [WalletController::class, 'balance'])->name('balance');
        Route::get('/deriv-balance', [WalletController::class, 'getDerivBalance'])->name('deriv-balance');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');

        // Transfer Services
        Route::get('/send-money', [WalletController::class, 'sendMoney'])->name('send');
        Route::get('/bank-transfer', [WalletController::class, 'bankTransfer'])->name('bank');

        // Airtime & Bundles
        Route::get('/buy-airtime', [WalletController::class, 'buyAirtime'])->name('airtime');
        Route::get('/buy-bundles', [WalletController::class, 'buyBundles'])->name('bundles');

        // M-Pesa Services
        Route::prefix('mpesa')->name('mpesa.')->group(function () {
            Route::get('/deposit', [WalletController::class, 'mpesaDeposit'])->name('deposit');
            Route::get('/withdraw', [WalletController::class, 'mpesaWithdraw'])->name('withdraw');
        });

        // Deriv Services
        Route::prefix('deriv')->name('deriv.')->group(function () {
            // Deposit
            Route::get('/deposit', [WalletController::class, 'showDerivDepositForm'])->name('deposit');
            Route::post('/deposit', [WalletController::class, 'processDerivDeposit']);
            
            // Withdraw
            Route::get('/withdraw', [WalletController::class, 'showDerivWithdrawForm'])->name('withdraw');
            Route::post('/withdraw/initiate', [WalletController::class, 'initiateWithdrawal'])->name('withdraw.initiate');
            Route::post('/withdraw/verify', [WalletController::class, 'verifyWithdrawal'])->name('withdraw.verify');
            
            // Balance
            Route::get('/balance', [WalletController::class, 'getDerivBalance'])->name('balance');
        });
    });

    // Deriv Session Data (Authenticated)
    Route::get('/deriv/session-data', [DerivAuthController::class, 'getSessionData'])->name('deriv.session.data');
});

/*
|--------------------------------------------------------------------------
| Deriv Routes (Alternative Access)
|--------------------------------------------------------------------------
*/
Route::prefix('deriv')->group(function () {
    Route::get('/auth', [DerivAuthController::class, 'initiateOAuth'])->name('deriv.auth');
    Route::get('/callback', [DerivAuthController::class, 'handleCallback'])->name('deriv.callback');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {

    // Public API Routes
    Route::post('/login', [AuthController::class, 'apiLogin']);
    Route::post('/register', [AuthController::class, 'apiRegister']);
    Route::post('/send-otp', [AuthController::class, 'apiSendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'apiVerifyOtp']);
    Route::post('/reset-password', [AuthController::class, 'apiUpdatePassword']);

    // Protected API Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'apiLogout']);
        Route::get('/user', [AuthController::class, 'getAuthenticatedUser']);
    });
});



Route::prefix('mpesa')->group(function () {
    Route::post('deposit/callback', [MpesaController::class, 'handleDepositCallback'])
        ->name('mpesa.deposit.callback');

    Route::post('withdrawal/callback', [MpesaController::class, 'handleWithdrawalCallback'])
        ->name('mpesa.withdrawal.callback');
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('mpesa')->group(function () {
        Route::post('deposit', [MpesaController::class, 'deposit']);
        Route::post('withdraw', [MpesaController::class, 'withdraw']);
    });
});


Route::post('/mpesa/send-money', [MpesaController::class, 'sendMoney']);
Route::post('/mpesa/transfer-callback', [MpesaController::class, 'handleTransferCallback'])->name('mpesa.transfer.callback');

// Optional: Get transaction costs for frontend
Route::get('/transaction-costs/{type}', function ($type) {
    return response()->json([
        'status' => 'success',
        'data' => \App\Models\TransactionCost::where('transfer_type', $type)
            ->where('is_active', true)
            ->orderBy('min_amount')
            ->get()
    ]);
})->where('type', 'internal|external_mpesa|external_airtel');

// Get transfer history
Route::get('/money-transfers', function (Request $request) {
    $session = \App\Models\LoginSession::where('session_id', $request->session_id)->first();
    if (!$session) {
        return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
    }

    $transfers = \App\Models\MoneyTransfer::where('sender_wallet_id', $session->wallet_id)
        ->orWhere('recipient_wallet_id', $session->wallet_id)
        ->with(['sender', 'recipient'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    return response()->json([
        'status' => 'success',
        'data' => $transfers
    ]);
});



/*
|--------------------------------------------------------------------------
| Legal & Static Pages
|--------------------------------------------------------------------------
*/
Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');


Route::get('/about-us', function () {
    return view('community.about-us');
})->name('about');

Route::get('/contact-us', function () {
    return view('community.contact-us');
})->name('contact');

Route::get('/faq', function () {
    return view('community.faq');
})->name('faq');

Route::get('/community', function () {
    return view('community.community');
})->name('community');

Route::get('/help-center', function () {
    return view('community.help-center');
})->name('help-center');

Route::get('/careers', function () {
    return view('community.careers');
})->name('careers');

Route::get('/blog', function () {
    return view('community.blog');
})->name('blog');

Route::get('/press', function () {
    return view('community.press');
})->name('press');

Route::get('/cookies', function () {
    return view('legal.cookies');
})->name('cookies');

Route::get('/legal', function () {
    return view('legal.index');
})->name('legal');

Route::get('/features', function () {
    return view('legal.features');
})->name('features');

Route::get('/pricing', function () {
    return view('legal.pricing');
})->name('pricing');

Route::get('/security', function () {
    return view('legal.security');
})->name('security');

Route::get('/card', function () {
    return view('legal.card');
})->name('card');

Route::get('/mobile-app', function () {
    return view('legal.mobile-app');
})->name('mobile-app');


Route::get('welcome', function () {
    return view('welcome');
})->name('welcome');
