<?php

namespace App\Http\Controllers;

use App\Services\DerivService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    protected $derivService;

    public function __construct(DerivService $derivService)
    {
        $this->derivService = $derivService;
        $this->middleware('auth');
    }

    /**
     * Show the Deriv deposit form
     */
    public function showDerivDepositForm()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        try {
            $derivBalance = $this->derivService->getBalance();
        } catch (\Exception $e) {
            $derivBalance = null;
            Log::error('Failed to fetch Deriv balance: ' . $e->getMessage());
        }

        return view('wallet.deriv-deposit', [
            'wallet' => $wallet,
            'derivBalance' => $derivBalance,
            'exchangeRate' => $this->getExchangeRate(),
            'user' => $user
        ]);
    }

    /**
     * Process Deriv deposit request
     */
    public function processDerivDeposit(Request $request)
    {
        $validated = $request->validate([
            'cr_number' => 'required|string|min:8|max:12',
            'amount' => 'required|numeric|min:100|max:1000000', // Min 100 KES, Max 1,000,000 KES
            'deriv_account' => 'required|in:real,virtual'
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;
        $amount = $validated['amount'];
        
        // Check if user has sufficient balance
        if ($wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance.'
            ], 400);
        }

        // Calculate USD amount based on exchange rate
        $exchangeRate = $this->getExchangeRate();
        $usdAmount = $amount / $exchangeRate;
        $fee = $this->calculateFee($amount);
        $totalDeducted = $amount + $fee;

        try {
            DB::beginTransaction();

            // Deduct from user's wallet
            $wallet->decrement('balance', $totalDeducted);

            // Record transaction
            $transaction = new Transaction([
                'user_id' => $user->id,
                'type' => 'deriv_deposit',
                'amount' => $amount,
                'fee' => $fee,
                'description' => 'Deposit to Deriv ' . $validated['deriv_account'] . ' account',
                'status' => 'completed',
                'details' => [
                    'cr_number' => $validated['cr_number'],
                    'deriv_account' => $validated['deriv_account'],
                    'exchange_rate' => $exchangeRate,
                    'usd_amount' => $usdAmount
                ]
            ]);
            $transaction->save();

            // Transfer to Deriv
            $transferResult = $this->derivService->transferToAccount(
                $validated['cr_number'],
                $usdAmount,
                'Deposit from ' . config('app.name')
            );

            if (!$transferResult['success']) {
                throw new \Exception($transferResult['message'] ?? 'Transfer to Deriv failed');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deposit to Deriv account successful!',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deriv deposit failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Deposit failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the Deriv withdrawal form
     */
    public function showDerivWithdrawForm()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        try {
            $derivBalance = $this->derivService->getBalance();
        } catch (\Exception $e) {
            $derivBalance = null;
            Log::error('Failed to fetch Deriv balance: ' . $e->getMessage());
        }

        return view('wallet.deriv-withdraw', [
            'wallet' => $wallet,
            'derivBalance' => $derivBalance,
            'exchangeRate' => $this->getExchangeRate(),
            'user' => $user
        ]);
    }

    /**
     * Initiate Deriv withdrawal with email verification
     */
    public function initiateWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:10000', // Min $1, Max $10,000
        ]);

        $user = Auth::user();
        $usdAmount = $validated['amount'];
        
        try {
            // Check if Deriv has sufficient balance
            $derivBalance = $this->derivService->getBalance();
            if ($derivBalance < $usdAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient Deriv balance.'
                ], 400);
            }

            // Request email verification
            $verification = $this->derivService->requestWithdrawalVerification(
                $user->email,
                $usdAmount
            );

            if (!$verification['success']) {
                throw new \Exception($verification['message'] ?? 'Failed to initiate withdrawal');
            }

            // Store verification ID in session for the next step
            session(['withdrawal_verification_id' => $verification['verification_id']]);
            session(['withdrawal_amount' => $usdAmount]);

            return response()->json([
                'success' => true,
                'requires_verification' => true,
                'message' => 'Please check your email for the verification code.',
                'verification_id' => $verification['verification_id']
            ]);

        } catch (\Exception $e) {
            Log::error('Withdrawal initiation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify withdrawal with email code and process payment agent withdrawal
     */
    public function verifyWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'verification_code' => 'required|string|size:8', // 8-digit code from user's email
        ]);

        $user = Auth::user();
        $verificationId = session('withdrawal_verification_id');
        $usdAmount = session('withdrawal_amount');
        $paymentAgentId = config('services.deriv.payment_agent_id');

        if (!$verificationId || !$usdAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification session. Please try again.'
            ], 400);
        }

        if (empty($paymentAgentId)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment agent not configured. Please contact support.'
            ], 500);
        }

        DB::beginTransaction();

        try {
            // Process payment agent withdrawal with the verification code
            $withdrawalResult = $this->derivService->processPaymentAgentWithdrawal(
                $usdAmount,
                $validated['verification_code'],
                $paymentAgentId,
                $verificationId,
                'Withdrawal to Stepaka Wallet'
            );

            if (!$withdrawalResult['success']) {
                throw new \Exception($withdrawalResult['message'] ?? 'Withdrawal processing failed');
            }

            // Convert USD to KES using exchange rate
            $exchangeRate = $this->getExchangeRate();
            $amountKes = $usdAmount * $exchangeRate;
            $fee = $this->calculateFee($amountKes, 'withdraw');
            $totalCredited = $amountKes - $fee;

            // Add to user's wallet
            $wallet = $user->wallet;
            $wallet->increment('balance', $totalCredited);

            // Record transaction
            $transaction = new Transaction([
                'user_id' => $user->id,
                'type' => 'deriv_withdraw',
                'amount' => $amountKes,
                'fee' => $fee,
                'description' => 'Withdrawal from Deriv account',
                'status' => 'completed',
                'details' => [
                    'exchange_rate' => $exchangeRate,
                    'usd_amount' => $usdAmount,
                    'total_credited' => $totalCredited,
                    'transaction_id' => $withdrawalResult['transaction_id'] ?? null,
                    'payment_agent' => $withdrawalResult['paymentagent_name'] ?? null,
                    'is_dry_run' => $withdrawalResult['is_dry_run'] ?? false
                ]
            ]);
            $transaction->save();

            // Clear session data
            session()->forget(['withdrawal_verification_id', 'withdrawal_amount']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal from Deriv account successful!',
                'transaction' => $transaction
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current Deriv balance via API
     */
    public function getDerivBalance()
    {
        try {
            $balance = $this->derivService->getBalance();
            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch Deriv balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current exchange rate (KES to USD)
     */
    private function getExchangeRate()
    {
        // In a real app, this would fetch from a live API or database
        // For now, we'll use a fixed rate
        return 150.50; // 1 USD = 150.50 KES
    }

    /**
     * Calculate transaction fee
     */
    private function calculateFee($amount, $type = 'deposit')
    {
        // 1% fee, min 10 KES, max 500 KES for deposits
        // 0.5% fee, min 5 KES, max 250 KES for withdrawals
        if ($type === 'deposit') {
            $fee = $amount * 0.01;
            return min(max($fee, 10), 500);
        } else {
            $fee = $amount * 0.005;
            return min(max($fee, 5), 250);
        }
    }

    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        
        try {
            $derivBalance = $this->derivService->getBalance();
        } catch (\Exception $e) {
            $derivBalance = null;
            Log::error('Failed to fetch Deriv balance: ' . $e->getMessage());
        }

        return view('wallet.index', [
            'wallet' => $wallet,
            'derivBalance' => $derivBalance,
            'conversionRate' => $this->getConversionRate()
        ]);
    }

    public function depositToDeriv(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'cr_number' => 'required|string|min:8|max:12'
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $crNumber = $request->cr_number;

        // Check if user has sufficient balance
        if ($user->wallet->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient funds'
            ], 400);
        }

        // Process Deriv transfer
        $transfer = $this->derivService->transferToAccount($crNumber, $amount);

        if (!$transfer['success']) {
            return response()->json([
                'success' => false,
                'message' => $transfer['message'] ?? 'Transfer failed'
            ], 400);
        }

        // Deduct from wallet and record transaction
        $transaction = $this->recordTransaction(
            $user->id,
            'deriv_deposit',
            $amount,
            'Deposit to Deriv account ' . $crNumber
        );

        return response()->json([
            'success' => true,
            'message' => 'Deposit successful',
            'data' => [
                'transaction' => $transaction,
                'new_balance' => $user->wallet->fresh()->balance
            ]
        ]);
    }

    public function withdrawFromDeriv(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'cr_number' => 'required|string|min:8|max:12'
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $crNumber = $request->cr_number;

        // Process Deriv withdrawal
        $transfer = $this->derivService->transferToAccount($crNumber, $amount, 'Withdrawal to wallet');

        if (!$transfer['success']) {
            return response()->json([
                'success' => false,
                'message' => $transfer['message'] ?? 'Withdrawal failed'
            ], 400);
        }

        // Add to wallet and record transaction
        $transaction = $this->recordTransaction(
            $user->id,
            'deriv_withdrawal',
            -$amount, // Negative amount for withdrawal
            'Withdrawal from Deriv account ' . $crNumber
        );

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal successful',
            'data' => [
                'transaction' => $transaction,
                'new_balance' => $user->wallet->fresh()->balance
            ]
        ]);
    }

    private function getConversionRate()
    {
        // Implement your conversion rate logic here
        // This could be from a database or external API
        return 150.00; // Example rate
    }

    private function recordTransaction($userId, $type, $amount, $description)
    {
        // Create transaction record
        return Transaction::create([
            'user_id' => $userId,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
            'status' => 'completed'
        ]);
    }
}