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
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home Route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Legal & Static Pages
Route::get('/terms', function () {
    return view('legal.terms'); 
})->name('terms');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    
    // Standard Authentication
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('signup');
    
    // Password Reset Flow
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('request');
        Route::post('/forgot', [AuthController::class, 'sendOtp'])->name('email');
        Route::get('/verify-otp/{wallet_id?}', [AuthController::class, 'showVerifyOtpForm'])->name('verify');
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.submit');
        Route::get('/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('reset');
        Route::post('/reset', [AuthController::class, 'updatePassword'])->name('update');
    });
    
    // Deriv OAuth Authentication
    Route::prefix('auth/deriv')->name('auth.deriv.')->group(function () {
        Route::get('/', [DerivAuthController::class, 'showDerivAuth'])->name('index');
        Route::get('/oauth', [DerivAuthController::class, 'initiateOAuth'])->name('oauth');
        Route::get('/callback', [DerivAuthController::class, 'handleCallback'])->name('callback');
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
            Route::get('/deposit', [WalletController::class, 'derivDeposit'])->name('deposit');
            Route::get('/withdraw', [WalletController::class, 'derivWithdraw'])->name('withdraw');
        });
    });
    
    // Deriv Session Data
    Route::get('/deriv/session-data', [DerivAuthController::class, 'getSessionData'])->name('deriv.session.data');
    
    // Money Transfer History
    Route::get('/money-transfers', function(Request $request) {
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
    })->name('transfers.history');
});

/*
|--------------------------------------------------------------------------
| M-Pesa Routes
|--------------------------------------------------------------------------
*/

Route::prefix('mpesa')->name('mpesa.')->group(function () {
    
    // Callback Routes (Public - for M-Pesa to call)
    Route::post('/deposit/callback', [MpesaController::class, 'handleDepositCallback'])->name('deposit.callback');
    Route::post('/withdrawal/callback', [MpesaController::class, 'handleWithdrawalCallback'])->name('withdrawal.callback');
    Route::post('/transfer/callback', [MpesaController::class, 'handleTransferCallback'])->name('transfer.callback');
    
    // Protected M-Pesa Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/deposit', [MpesaController::class, 'deposit'])->name('deposit');
        Route::post('/withdraw', [MpesaController::class, 'withdraw'])->name('withdraw');
    });
    
    // Send Money Route (consider adding auth middleware if needed)
    Route::post('/send-money', [MpesaController::class, 'sendMoney'])->name('send');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->name('api.')->group(function () {
    
    // Public API Routes
    Route::post('/login', [AuthController::class, 'apiLogin'])->name('login');
    Route::post('/register', [AuthController::class, 'apiRegister'])->name('register');
    Route::post('/send-otp', [AuthController::class, 'apiSendOtp'])->name('send.otp');
    Route::post('/verify-otp', [AuthController::class, 'apiVerifyOtp'])->name('verify.otp');
    Route::post('/reset-password', [AuthController::class, 'apiUpdatePassword'])->name('reset.password');
    
    // Protected API Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'apiLogout'])->name('logout');
        Route::get('/user', [AuthController::class, 'getAuthenticatedUser'])->name('user');
    });
});

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
*/

// Transaction Costs Lookup
Route::get('/transaction-costs/{type}', function($type) {
    return response()->json([
        'status' => 'success',
        'data' => \App\Models\TransactionCost::where('transfer_type', $type)
            ->where('is_active', true)
            ->orderBy('min_amount')
            ->get()
    ]);
})->where('type', 'internal|external_mpesa|external_airtel')->name('transaction.costs');

/*
|--------------------------------------------------------------------------
| Legacy/Alternative Routes
|--------------------------------------------------------------------------
*/

// Alternative Deriv Routes 
Route::prefix('deriv')->name('deriv.')->group(function () {
    Route::get('/auth', [DerivAuthController::class, 'initiateOAuth'])->name('auth');
    Route::get('/callback', [DerivAuthController::class, 'handleCallback'])->name('callback');
});