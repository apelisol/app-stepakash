<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MpesaDeposit;
use App\Models\MpesaWithdrawal;
use App\Models\MoneyTransfer;
use App\Models\TransactionCost;
use App\Models\Ledger;
use App\Models\Outbox;
use App\Models\LoginSession;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\MpesaWithdrawalSuccessful;
use App\Mail\MpesaDepositSuccessful;
use App\Mail\MoneyTransferSent;
use App\Mail\MoneyTransferReceived;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    /**
     * Initiate M-Pesa deposit
     */
    public function deposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'amount' => 'required|numeric|min:10',
            'session_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // Verify session
        $session = LoginSession::where('session_id', $request->session_id)->first();
        if (!$session) {
            return response()->json([
                'status' => 'error',
                'code' => 'UNAUTHORIZED',
                'message' => 'User not logged in',
                'data' => null
            ], 401);
        }

        $customer = Customer::where('wallet_id', $session->wallet_id)->first();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'code' => 'CUSTOMER_NOT_FOUND',
                'message' => 'Customer not found',
                'data' => null
            ], 404);
        }

        $phone = $this->formatPhoneNumber($request->phone);
        $amount = $request->amount;
        $invoiceNumber = "STEPAKASH-" . $customer->wallet_id;

        try {
            $response = $this->mpesaService->stkPush(
                $phone,
                $amount,
                $invoiceNumber,
                route('mpesa.deposit.callback')
            );

            if ($response['ResponseCode'] == '0') {
                // Save the deposit request
                $deposit = MpesaDeposit::create([
                    'wallet_id' => $customer->wallet_id,
                    'phone' => $phone,
                    'amount' => $amount,
                    'MerchantRequestID' => $response['MerchantRequestID'],
                    'CheckoutRequestID' => $response['CheckoutRequestID'],
                    'paid' => false,
                    'created_on' => Carbon::now()
                ]);

                return response()->json([
                    'status' => 'success',
                    'code' => 'DEPOSIT_INITIATED',
                    'message' => 'Please complete the deposit of KES ' . number_format($amount, 2) . ' on your phone to update your balance.',
                    'data' => [
                        'transaction_id' => $deposit->id,
                        'amount' => $amount,
                        'phone' => $phone,
                        'merchant_request_id' => $response['MerchantRequestID'],
                        'checkout_request_id' => $response['CheckoutRequestID'],
                        'timestamp' => Carbon::now()->toDateTimeString()
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 'MPESA_ERROR',
                    'message' => $response['errorMessage'] ?? 'Failed to initiate M-Pesa deposit',
                    'data' => [
                        'mpesa_response' => $response
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Deposit Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 'SERVER_ERROR',
                'message' => 'Failed to process M-Pesa deposit',
                'data' => null
            ], 500);
        }
    }

    /**
     * Initiate M-Pesa withdrawal
     */
    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'amount' => 'required|numeric|min:10',
            'session_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // Verify session
        $session = LoginSession::where('session_id', $request->session_id)->first();
        if (!$session) {
            return response()->json([
                'status' => 'error',
                'code' => 'UNAUTHORIZED',
                'message' => 'User not logged in',
                'data' => null
            ], 401);
        }

        $customer = Customer::where('wallet_id', $session->wallet_id)->first();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'code' => 'CUSTOMER_NOT_FOUND',
                'message' => 'Customer not found',
                'data' => null
            ], 404);
        }

        // Check customer balance
        $balance = $this->calculateBalance($customer->wallet_id);
        if ($balance < $request->amount) {
            return response()->json([
                'status' => 'error',
                'code' => 'INSUFFICIENT_FUNDS',
                'message' => 'Insufficient funds in your wallet',
                'data' => [
                    'current_balance' => $balance,
                    'requested_amount' => $request->amount
                ]
            ], 400);
        }

        // Check for duplicate withdrawal
        $duplicateCheck = $this->checkDuplicateWithdrawal($customer->wallet_id, $request->phone, $request->amount);
        if ($duplicateCheck['is_duplicate']) {
            $message = "Hi " . explode(' ', $customer->fullname)[0] . ", duplicate withdrawal attempt of KES " . 
                number_format($request->amount, 2) . " blocked for security. Contact support if this wasn't you. Time: " . 
                Carbon::now()->format('d/m/Y H:i');
            
            Outbox::create([
                'receiver' => $customer->phone,
                'message' => $message,
                'created_on' => Carbon::now(),
                'status' => 'sent'
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'DUPLICATE_TRANSACTION',
                'message' => 'Duplicate transaction detected. Please wait ' . 
                    $duplicateCheck['wait_time'] . ' seconds before making another withdrawal of the same amount.',
                'data' => [
                    'wait_time' => $duplicateCheck['wait_time'],
                    'last_transaction_time' => $duplicateCheck['last_transaction_time']
                ]
            ], 400);
        }

        $phone = $this->formatPhoneNumber($request->phone);
        $amount = $request->amount;
        $transactionId = Str::random(9);
        $transactionNumber = $this->generateTransactionNumber();

        try {
            $response = $this->mpesaService->b2cRequest(
                $phone,
                $amount,
                $transactionId,
                route('mpesa.withdrawal.callback')
            );

            if ($response['ResponseCode'] == '0') {
                // Save withdrawal request
                $withdrawal = MpesaWithdrawal::create([
                    'transaction_id' => $transactionId,
                    'transaction_number' => $transactionNumber,
                    'wallet_id' => $customer->wallet_id,
                    'amount' => $amount,
                    'phone' => $phone,
                    'conversationID' => $response['ConversationID'],
                    'OriginatorConversationID' => $response['OriginatorConversationID'],
                    'ResponseCode' => $response['ResponseCode'],
                    'withdraw' => false,
                    'paid' => false,
                    'request_date' => Carbon::now()
                ]);

                // Debit customer's wallet immediately
                $debitResult = $this->debitWallet(
                    $customer->wallet_id, 
                    $amount, 
                    $transactionId, 
                    $transactionNumber, 
                    'Withdrawal to M-Pesa'
                );

                if (!$debitResult) {
                    throw new \Exception('Failed to debit wallet');
                }

                // Send confirmation SMS
                $message = "Hi " . explode(' ', $customer->fullname)[0] . ", your withdrawal request of KES " . 
                    number_format($amount, 2) . " is being processed. You will receive confirmation once completed. Ref: $transactionId";
                
                Outbox::create([
                    'receiver' => $customer->phone,
                    'message' => $message,
                    'created_on' => Carbon::now(),
                    'status' => 'sent'
                ]);

                return response()->json([
                    'status' => 'success',
                    'code' => 'WITHDRAWAL_INITIATED',
                    'message' => 'Withdrawal request of KES ' . number_format($amount, 2) . ' initiated successfully.',
                    'data' => [
                        'transaction_id' => $withdrawal->id,
                        'amount' => $amount,
                        'phone' => $phone,
                        'conversation_id' => $response['ConversationID'],
                        'originator_conversation_id' => $response['OriginatorConversationID'],
                        'timestamp' => Carbon::now()->toDateTimeString(),
                        'current_balance' => $this->calculateBalance($customer->wallet_id)
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'code' => 'MPESA_ERROR',
                    'message' => $response['errorMessage'] ?? 'Failed to initiate M-Pesa withdrawal',
                    'data' => [
                        'mpesa_response' => $response
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Withdrawal Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 'SERVER_ERROR',
                'message' => 'Failed to process M-Pesa withdrawal',
                'data' => null
            ], 500);
        }
    }

    /**
     * Handle M-Pesa deposit callback
     */
    public function handleDepositCallback(Request $request)
    {
        $callbackData = $request->getContent();
        Log::channel('mpesa')->info('M-Pesa Deposit Callback: ' . $callbackData);

        try {
            $data = json_decode($callbackData, true);
            
            if (!isset($data['Body']['stkCallback'])) {
                throw new \Exception('Invalid callback structure');
            }

            $callback = $data['Body']['stkCallback'];
            $resultCode = $callback['ResultCode'];
            $merchantRequestId = $callback['MerchantRequestID'];
            $checkoutRequestId = $callback['CheckoutRequestID'];

            if ($resultCode != 0) {
                // Failed transaction
                $this->updateFailedDeposit($merchantRequestId, $checkoutRequestId, $resultCode, $callback['ResultDesc']);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed']);
            }

            // Successful transaction
            if (!isset($callback['CallbackMetadata']['Item'])) {
                throw new \Exception('Missing callback metadata');
            }

            $items = collect($callback['CallbackMetadata']['Item'])->pluck('Value', 'Name');

            $this->processSuccessfulDeposit(
                $merchantRequestId,
                $checkoutRequestId,
                $items['MpesaReceiptNumber'],
                $items['Amount'],
                $items['TransactionDate'],
                $items['PhoneNumber']
            );

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::channel('mpesa')->error('Deposit Callback Error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error processing callback']);
        }
    }

    /**
     * Handle M-Pesa withdrawal callback
     */
    public function handleWithdrawalCallback(Request $request)
    {
        $callbackData = $request->getContent();
        Log::channel('mpesa')->info('M-Pesa Withdrawal Callback: ' . $callbackData);

        try {
            $data = json_decode($callbackData, true);
            
            if (!isset($data['Result'])) {
                throw new \Exception('Invalid callback structure');
            }

            $result = $data['Result'];
            $resultCode = $result['ResultCode'];
            $conversationId = $result['ConversationID'];
            $originatorConversationId = $result['OriginatorConversationID'];

            if ($resultCode != 0) {
                // Failed transaction
                $this->updateFailedWithdrawal($originatorConversationId, $conversationId, $resultCode, $result['ResultDesc']);
                return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed']);
            }

            // Successful transaction
            if (!isset($result['ResultParameters']['ResultParameter'])) {
                throw new \Exception('Missing result parameters');
            }

            $params = collect($result['ResultParameters']['ResultParameter'])
                ->pluck('Value', 'Key')
                ->toArray();

            $this->processSuccessfulWithdrawal(
                $resultCode,
                $result['ResultDesc'],
                $originatorConversationId,
                $conversationId,
                $result['TransactionID'],
                $params['TransactionAmount'],
                $params['ReceiverPartyPublicName'],
                $params['TransactionCompletedDateTime'],
                $params['B2CUtilityAccountAvailableFunds'],
                $params['B2CWorkingAccountAvailableFunds']
            );

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::channel('mpesa')->error('Withdrawal Callback Error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error processing callback']);
        }
    }

    /**
     * Process successful deposit
     */
    protected function processSuccessfulDeposit($merchantRequestId, $checkoutRequestId, $mpesaReceipt, $amount, $transactionDate, $phone)
    {
        $deposit = MpesaDeposit::where('MerchantRequestID', $merchantRequestId)
            ->where('CheckoutRequestID', $checkoutRequestId)
            ->first();

        if (!$deposit) {
            throw new \Exception('Deposit record not found');
        }

        if ($deposit->paid) {
            Log::channel('mpesa')->warning('Duplicate deposit processing attempt: ' . $mpesaReceipt);
            return;
        }

        // Update deposit record
        $deposit->update([
            'paid' => true,
            'txn' => $mpesaReceipt,
            'TransactionDate' => $transactionDate,
            'updated_on' => Carbon::now()
        ]);

        // Credit customer's wallet
        $creditResult = $this->creditWallet(
            $deposit->wallet_id, 
            $amount, 
            $mpesaReceipt, 
            'MPESA Deposit'
        );

        if (!$creditResult) {
            throw new \Exception('Failed to credit wallet');
        }

        // Get customer details
        $customer = $deposit->customer;
        $balance = $this->calculateBalance($customer->wallet_id);

        // Send notifications
        $this->sendDepositSuccessfulNotifications($customer, $amount, $mpesaReceipt, $balance);
    }

    /**
     * Process successful withdrawal
     */
    protected function processSuccessfulWithdrawal($resultCode, $resultDesc, $originatorConversationId, 
        $conversationId, $mpesaReceipt, $amount, $receiverName, $transactionDate, $utilityBalance, $workingBalance)
    {
        $withdrawal = MpesaWithdrawal::where('OriginatorConversationID', $originatorConversationId)
            ->where('conversationID', $conversationId)
            ->first();

        if (!$withdrawal) {
            throw new \Exception('Withdrawal record not found');
        }

        if ($withdrawal->paid) {
            Log::channel('mpesa')->warning('Duplicate withdrawal processing attempt: ' . $mpesaReceipt);
            return;
        }

        // Update withdrawal record
        $withdrawal->update([
            'paid' => true,
            'MpesaReceiptNumber' => $mpesaReceipt,
            'receiverPartyPublicName' => $receiverName,
            'transactionCompletedDateTime' => $transactionDate,
            'b2cUtilityAccountAvailableFunds' => $utilityBalance,
            'b2cWorkingAccountAvailableFunds' => $workingBalance,
            'result_code' => $resultCode,
            'result_desc' => $resultDesc,
            'updated_on' => Carbon::now()
        ]);

        // Get customer details
        $customer = $withdrawal->customer;
        $balance = $this->calculateBalance($customer->wallet_id);

        // Send notifications
        $this->sendWithdrawalSuccessfulNotifications($customer, $amount, $mpesaReceipt, $balance);
    }

    /**
     * Update failed deposit
     */
    protected function updateFailedDeposit($merchantRequestId, $checkoutRequestId, $resultCode, $resultDesc)
    {
        MpesaDeposit::where('MerchantRequestID', $merchantRequestId)
            ->where('CheckoutRequestID', $checkoutRequestId)
            ->update([
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'updated_on' => Carbon::now()
            ]);
    }

    /**
     * Update failed withdrawal
     */
    protected function updateFailedWithdrawal($originatorConversationId, $conversationId, $resultCode, $resultDesc)
    {
        MpesaWithdrawal::where('OriginatorConversationID', $originatorConversationId)
            ->where('conversationID', $conversationId)
            ->update([
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
                'updated_on' => Carbon::now()
            ]);
    }

    /**
     * Check for duplicate withdrawal
     */
    protected function checkDuplicateWithdrawal($walletId, $phone, $amount)
    {
        $timeThreshold = 30; // 30 seconds
        $thresholdTime = Carbon::now()->subSeconds($timeThreshold);

        $lastWithdrawal = MpesaWithdrawal::where('wallet_id', $walletId)
            ->where('phone', $this->formatPhoneNumber($phone))
            ->where('amount', $amount)
            ->where('request_date', '>=', $thresholdTime)
            ->orderBy('request_date', 'desc')
            ->first();

        if ($lastWithdrawal) {
            $timeDiff = Carbon::now()->diffInSeconds($lastWithdrawal->request_date);
            $waitTime = $timeThreshold - $timeDiff;

            return [
                'is_duplicate' => true,
                'wait_time' => $waitTime,
                'last_transaction_time' => $lastWithdrawal->request_date
            ];
        }

        return ['is_duplicate' => false];
    }

    /**
     * Calculate customer balance with enhanced safety and accuracy
     */
    protected function calculateBalance($walletId)
    {
        try {
            // Verify wallet exists
            if (!Wallet::where('id', $walletId)->exists()) {
                Log::error("Wallet not found: $walletId");
                throw new \Exception('Wallet not found');
            }

            // Get sum of all verified credit transactions
            $credits = Ledger::where('wallet_id', $walletId)
                ->where('cr_dr', 'cr')
                ->where('status', true)
                ->sum('amount');

            // Get sum of all verified debit transactions
            $debits = Ledger::where('wallet_id', $walletId)
                ->where('cr_dr', 'dr')
                ->where('status', true)
                ->sum('amount');

            // Verify amounts are numeric
            if (!is_numeric($credits) || !is_numeric($debits)) {
                Log::error("Invalid amount types - Credits: $credits, Debits: $debits");
                throw new \Exception('Invalid transaction amounts');
            }

            $balance = $credits - $debits;

            // Log suspicious activity (negative balance if not allowed)
            if ($balance < 0) {
                Log::warning("Negative balance detected for wallet: $walletId", [
                    'balance' => $balance,
                    'credits' => $credits,
                    'debits' => $debits
                ]);
            }

            return (float) number_format($balance, 2, '.', '');
        } catch (\Exception $e) {
            Log::error("Balance calculation failed for wallet $walletId: " . $e->getMessage());
            throw $e; // Re-throw to be handled by the caller
        }
    }

    /**
     * Credit customer wallet with transaction safety
     */
    protected function creditWallet($walletId, $amount, $reference, $description)
    {
        DB::beginTransaction();
        
        try {
            // Validate input
            if (!is_numeric($amount) || $amount <= 0) {
                throw new \InvalidArgumentException('Invalid amount');
            }

            $amount = (float) number_format($amount, 2, '.', '');
            $transactionId = Str::uuid()->toString();
            $transactionNumber = $this->generateTransactionNumber();
            $receiptNo = 'CR' . time() . Str::random(6);

            // Create ledger entry
            $ledger = Ledger::create([
                'wallet_id' => $walletId,
                'transaction_id' => $transactionId,
                'transaction_number' => $transactionNumber,
                'receipt_no' => $receiptNo,
                'description' => $description,
                'pay_method' => 'MPESA',
                'trans_id' => $reference,
                'amount' => $amount,
                'cr_dr' => 'cr',
                'trans_date' => Carbon::now(),
                'currency' => 'KES',
                'status' => true,
                'created_at' => Carbon::now()
            ]);

            // Get updated balance
            $balance = $this->calculateBalance($walletId);

            // Log the transaction
            Log::info("Wallet $walletId credited", [
                'amount' => $amount,
                'reference' => $reference,
                'new_balance' => $balance,
                'transaction_id' => $transactionId
            ]);

            DB::commit();
            
            // Invalidate any cached balance
            Cache::forget("wallet_balance_{$walletId}");
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'new_balance' => $balance
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to credit wallet $walletId: " . $e->getMessage(), [
                'amount' => $amount ?? null,
                'reference' => $reference ?? null
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

   /**
     * Debit customer wallet with transaction safety
     */
    protected function debitWallet($walletId, $amount, $transactionId, $transactionNumber, $description)
    {
        DB::beginTransaction();
        
        try {
            // Validate input
            if (!is_numeric($amount) || $amount <= 0) {
                throw new \InvalidArgumentException('Invalid amount');
            }

            $amount = (float) number_format($amount, 2, '.', '');
            $receiptNo = 'DR' . time() . Str::random(6);

            // Check available balance before debiting
            $currentBalance = $this->calculateBalance($walletId);
            if ($currentBalance < $amount) {
                throw new \RuntimeException('Insufficient funds');
            }

            // Create ledger entry
            $ledger = Ledger::create([
                'wallet_id' => $walletId,
                'transaction_id' => $transactionId,
                'transaction_number' => $transactionNumber,
                'receipt_no' => $receiptNo,
                'description' => $description,
                'pay_method' => 'MPESA',
                'trans_id' => $transactionId,
                'amount' => $amount,
                'cr_dr' => 'dr',
                'trans_date' => Carbon::now(),
                'currency' => 'KES',
                'status' => true,
                'created_at' => Carbon::now()
            ]);

            // Get updated balance
            $newBalance = $this->calculateBalance($walletId);

            // Log the transaction
            Log::info("Wallet $walletId debited", [
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'previous_balance' => $currentBalance,
                'new_balance' => $newBalance,
                'reference' => $transactionNumber
            ]);

            DB::commit();
            
            // Invalidate any cached balance
            Cache::forget("wallet_balance_{$walletId}");
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'new_balance' => $newBalance
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to debit wallet $walletId: " . $e->getMessage(), [
                'amount' => $amount ?? null,
                'transaction_id' => $transactionId ?? null
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Send deposit successful notifications
     */
    protected function sendDepositSuccessfulNotifications($customer, $amount, $mpesaCode, $balance)
    {
        $nameParts = explode(' ', $customer->fullname);
        $firstName = $nameParts[0] ?? 'Customer';
        $formattedDate = Carbon::now()->format('d/m/Y H:i');

        // SMS
        $smsMessage = view('sms.deposit_successful', [
            'customerName' => $firstName,
            'amount' => $amount,
            'mpesaCode' => $mpesaCode,
            'balance' => $balance,
            'transactionDate' => $formattedDate
        ])->render();

        Outbox::create([
            'receiver' => $customer->phone,
            'message' => $smsMessage,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);

        // Email if customer has email
        if ($customer->deriv_email) {
            Mail::to($customer->deriv_email)->send(
                new MpesaDepositSuccessful(
                    $customer,
                    $amount,
                    $mpesaCode,
                    $balance,
                    route('wallet.dashboard')
                )
            );
        }
    }

    /**
     * Send withdrawal successful notifications
     */
    protected function sendWithdrawalSuccessfulNotifications($customer, $amount, $mpesaCode, $balance)
    {
        $nameParts = explode(' ', $customer->fullname);
        $firstName = $nameParts[0] ?? 'Customer';
        $formattedDate = Carbon::now()->format('d/m/Y H:i');

        // SMS
        $smsMessage = view('sms.withdrawal_successful', [
            'customerName' => $firstName,
            'amount' => $amount,
            'mpesaCode' => $mpesaCode,
            'balance' => $balance,
            'transactionDate' => $formattedDate
        ])->render();

        Outbox::create([
            'receiver' => $customer->phone,
            'message' => $smsMessage,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);

        // Email if customer has email
        if ($customer->deriv_email) {
            Mail::to($customer->deriv_email)->send(
                new MpesaWithdrawalSuccessful(
                    $customer,
                    $amount,
                    $mpesaCode,
                    $balance,
                    route('wallet.dashboard')
                )
            );
        }
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber($phone)
    {
        $phone = str_replace(' ', '', $phone);
        return preg_replace('/^(?:\+?254|0)?/', '254', $phone);
    }

    /**
     * Generate transaction number
     */
    protected function generateTransactionNumber()
    {
        // Get current timestamp components
        $time = Carbon::now();
        $seconds = $time->second; // 0-59
        $minutes = $time->minute; // 0-59
        
        // Generate base random string
        $randomString = Str::upper(Str::random(4));
        
        // Insert timestamp components
        $transactionNumber = 'SK' . 
                            substr($randomString, 0, 2) . 
                            $seconds . 
                            substr($randomString, 2, 2) . 
                            $minutes;
        // Ensure transaction number is 8 characters long
        $transactionNumber = substr($transactionNumber, 0, 8); // SK + 6 chars
        
        return $transactionNumber;
    }



    /**
     * Send money to phone number (internal user or external M-Pesa/Airtel)
     */
    public function sendMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_phone' => 'required|string',
            'amount' => 'required|numeric|min:10',
            'session_id' => 'required',
            'recipient_name' => 'nullable|string',
            'network' => 'nullable|in:mpesa,airtel' // Optional: specify network for external transfers
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // Verify session
        $session = LoginSession::where('session_id', $request->session_id)->first();
        if (!$session) {
            return response()->json([
                'status' => 'error',
                'code' => 'UNAUTHORIZED',
                'message' => 'User not logged in',
                'data' => null
            ], 401);
        }

        $sender = Customer::where('wallet_id', $session->wallet_id)->first();
        if (!$sender) {
            return response()->json([
                'status' => 'error',
                'code' => 'CUSTOMER_NOT_FOUND',
                'message' => 'Sender not found',
                'data' => null
            ], 404);
        }

        $recipientPhone = $this->formatPhoneNumber($request->recipient_phone);
        $amount = $request->amount;

        // Check if recipient is a registered user
        $recipient = Customer::where('phone', $recipientPhone)->first();
        $isInternalTransfer = !is_null($recipient);

        // Calculate transaction cost
        $transactionCost = $this->calculateTransactionCost($amount, $isInternalTransfer, $request->network);
        $totalAmount = $amount + $transactionCost;

        // Check sender balance
        $senderBalance = $this->calculateBalance($sender->wallet_id);
        if ($senderBalance < $totalAmount) {
            return response()->json([
                'status' => 'error',
                'code' => 'INSUFFICIENT_FUNDS',
                'message' => 'Insufficient funds. You need KES ' . number_format($totalAmount, 2) . ' (Amount: ' . number_format($amount, 2) . ' + Fee: ' . number_format($transactionCost, 2) . ')',
                'data' => [
                    'current_balance' => $senderBalance,
                    'required_amount' => $totalAmount,
                    'transfer_amount' => $amount,
                    'transaction_fee' => $transactionCost
                ]
            ], 400);
        }

        // Check for duplicate transfer
        $duplicateCheck = $this->checkDuplicateTransfer($sender->wallet_id, $recipientPhone, $amount);
        if ($duplicateCheck['is_duplicate']) {
            return response()->json([
                'status' => 'error',
                'code' => 'DUPLICATE_TRANSACTION',
                'message' => 'Duplicate transaction detected. Please wait ' . 
                    $duplicateCheck['wait_time'] . ' seconds before making another transfer of the same amount.',
                'data' => [
                    'wait_time' => $duplicateCheck['wait_time'],
                    'last_transaction_time' => $duplicateCheck['last_transaction_time']
                ]
            ], 400);
        }

        try {
            DB::beginTransaction();

            $transactionId = Str::random(9);
            $transactionNumber = $this->generateTransactionNumber();

            if ($isInternalTransfer) {
                // Internal transfer (wallet to wallet)
                $result = $this->processInternalTransfer($sender, $recipient, $amount, $transactionCost, $transactionId, $transactionNumber);
            } else {
                // External transfer (to M-Pesa/Airtel)
                $result = $this->processExternalTransfer($sender, $recipientPhone, $amount, $transactionCost, $transactionId, $transactionNumber, $request->recipient_name, $request->network);
            }

            if ($result['success']) {
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'code' => $result['code'],
                    'message' => $result['message'],
                    'data' => $result['data']
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'code' => $result['code'],
                    'message' => $result['message'],
                    'data' => $result['data']
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Money Transfer Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'code' => 'SERVER_ERROR',
                'message' => 'Failed to process money transfer',
                'data' => null
            ], 500);
        }
    }

    /**
     * Process internal transfer (wallet to wallet)
     */
    protected function processInternalTransfer($sender, $recipient, $amount, $transactionCost, $transactionId, $transactionNumber)
    {
        try {
            // Create transfer record
            $transfer = MoneyTransfer::create([
                'transaction_id' => $transactionId,
                'transaction_number' => $transactionNumber,
                'sender_wallet_id' => $sender->wallet_id,
                'recipient_wallet_id' => $recipient->wallet_id,
                'recipient_phone' => $recipient->phone,
                'recipient_name' => $recipient->fullname,
                'amount' => $amount,
                'transaction_cost' => $transactionCost,
                'transfer_type' => 'internal',
                'status' => 'completed',
                'created_at' => Carbon::now()
            ]);

            // Debit sender's wallet (amount + fee)
            $this->debitWallet(
                $sender->wallet_id,
                $amount + $transactionCost,
                $transactionId,
                $transactionNumber,
                'Money transfer to ' . $recipient->fullname . ' (' . $recipient->phone . ')'
            );

            // Credit recipient's wallet
            $this->creditWallet(
                $recipient->wallet_id,
                $amount,
                $transactionId,
                'Money received from ' . $sender->fullname . ' (' . $sender->phone . ')'
            );

            // Record transaction cost as revenue
            if ($transactionCost > 0) {
                $this->recordTransactionRevenue($transactionCost, $transactionId, 'internal_transfer');
            }

            // Send notifications
            $this->sendInternalTransferNotifications($sender, $recipient, $amount, $transactionCost, $transactionId);

            $senderBalance = $this->calculateBalance($sender->wallet_id);
            $recipientBalance = $this->calculateBalance($recipient->wallet_id);

            return [
                'success' => true,
                'code' => 'TRANSFER_COMPLETED',
                'message' => 'Money transferred successfully to ' . $recipient->fullname,
                'data' => [
                    'transaction_id' => $transfer->id,
                    'amount' => $amount,
                    'transaction_fee' => $transactionCost,
                    'recipient_name' => $recipient->fullname,
                    'recipient_phone' => $recipient->phone,
                    'sender_balance' => $senderBalance,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Internal Transfer Error: ' . $e->getMessage());
            return [
                'success' => false,
                'code' => 'TRANSFER_FAILED',
                'message' => 'Failed to complete internal transfer',
                'data' => null
            ];
        }
    }

    /**
     * Process external transfer (to M-Pesa/Airtel)
     */
    protected function processExternalTransfer($sender, $recipientPhone, $amount, $transactionCost, $transactionId, $transactionNumber, $recipientName = null, $network = 'mpesa')
    {
        try {
            // Create transfer record
            $transfer = MoneyTransfer::create([
                'transaction_id' => $transactionId,
                'transaction_number' => $transactionNumber,
                'sender_wallet_id' => $sender->wallet_id,
                'recipient_phone' => $recipientPhone,
                'recipient_name' => $recipientName,
                'amount' => $amount,
                'transaction_cost' => $transactionCost,
                'transfer_type' => 'external_' . $network,
                'status' => 'processing',
                'created_at' => Carbon::now()
            ]);

            // Debit sender's wallet immediately (amount + fee)
            $this->debitWallet(
                $sender->wallet_id,
                $amount + $transactionCost,
                $transactionId,
                $transactionNumber,
                'Money transfer to ' . $recipientPhone . ($recipientName ? ' (' . $recipientName . ')' : '')
            );

            // Record transaction cost as revenue
            if ($transactionCost > 0) {
                $this->recordTransactionRevenue($transactionCost, $transactionId, 'external_transfer_' . $network);
            }

            // Send to M-Pesa (for now, we'll use B2C for both M-Pesa and Airtel)
            $mpesaResponse = $this->mpesaService->b2cRequest(
                $recipientPhone,
                $amount,
                $transactionId,
                route('mpesa.transfer.callback')
            );

            if ($mpesaResponse['ResponseCode'] == '0') {
                // Update transfer with M-Pesa details
                $transfer->update([
                    'conversationID' => $mpesaResponse['ConversationID'],
                    'OriginatorConversationID' => $mpesaResponse['OriginatorConversationID'],
                    'ResponseCode' => $mpesaResponse['ResponseCode']
                ]);

                // Send notifications
                $this->sendExternalTransferNotifications($sender, $recipientPhone, $amount, $transactionCost, $transactionId, $recipientName);

                $senderBalance = $this->calculateBalance($sender->wallet_id);

                return [
                    'success' => true,
                    'code' => 'TRANSFER_INITIATED',
                    'message' => 'Money transfer initiated successfully. The recipient will receive the money shortly.',
                    'data' => [
                        'transaction_id' => $transfer->id,
                        'amount' => $amount,
                        'transaction_fee' => $transactionCost,
                        'recipient_phone' => $recipientPhone,
                        'recipient_name' => $recipientName,
                        'sender_balance' => $senderBalance,
                        'conversation_id' => $mpesaResponse['ConversationID'],
                        'timestamp' => Carbon::now()->toDateTimeString()
                    ]
                ];
            } else {
                // M-Pesa request failed, reverse the debit
                $this->creditWallet(
                    $sender->wallet_id,
                    $amount + $transactionCost,
                    $transactionId . '_REVERSAL',
                    'Transfer failed - amount reversed'
                );

                $transfer->update(['status' => 'failed']);

                return [
                    'success' => false,
                    'code' => 'MPESA_ERROR',
                    'message' => $mpesaResponse['errorMessage'] ?? 'Failed to initiate money transfer',
                    'data' => ['mpesa_response' => $mpesaResponse]
                ];
            }
        } catch (\Exception $e) {
            Log::error('External Transfer Error: ' . $e->getMessage());
            return [
                'success' => false,
                'code' => 'TRANSFER_FAILED',
                'message' => 'Failed to complete external transfer',
                'data' => null
            ];
        }
    }

    /**
     * Handle money transfer callback from M-Pesa
     */
    public function handleTransferCallback(Request $request)
    {
        $callbackData = $request->getContent();
        Log::channel('mpesa')->info('M-Pesa Transfer Callback: ' . $callbackData);

        try {
            $data = json_decode($callbackData, true);
            
            if (!isset($data['Result'])) {
                throw new \Exception('Invalid callback structure');
            }

            $result = $data['Result'];
            $resultCode = $result['ResultCode'];
            $conversationId = $result['ConversationID'];
            $originatorConversationId = $result['OriginatorConversationID'];

            $transfer = MoneyTransfer::where('OriginatorConversationID', $originatorConversationId)
                ->where('conversationID', $conversationId)
                ->first();

            if (!$transfer) {
                throw new \Exception('Transfer record not found');
            }

            if ($resultCode == 0) {
                // Successful transfer
                $params = collect($result['ResultParameters']['ResultParameter'])
                    ->pluck('Value', 'Key')
                    ->toArray();

                $transfer->update([
                    'status' => 'completed',
                    'MpesaReceiptNumber' => $result['TransactionID'],
                    'transactionCompletedDateTime' => $params['TransactionCompletedDateTime'] ?? Carbon::now(),
                    'result_code' => $resultCode,
                    'result_desc' => $result['ResultDesc']
                ]);

                // Send completion notifications
                $sender = Customer::where('wallet_id', $transfer->sender_wallet_id)->first();
                $this->sendTransferCompletionNotifications($sender, $transfer);
            } else {
                // Failed transfer - reverse the debit
                $transfer->update([
                    'status' => 'failed',
                    'result_code' => $resultCode,
                    'result_desc' => $result['ResultDesc']
                ]);

                // Credit back the sender's wallet (amount + fee)
                $this->creditWallet(
                    $transfer->sender_wallet_id,
                    $transfer->amount + $transfer->transaction_cost,
                    $transfer->transaction_id . '_REVERSAL',
                    'Transfer failed - amount reversed'
                );

                // Send failure notification
                $sender = Customer::where('wallet_id', $transfer->sender_wallet_id)->first();
                $this->sendTransferFailureNotifications($sender, $transfer);
            }

            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed successfully']);
        } catch (\Exception $e) {
            Log::channel('mpesa')->error('Transfer Callback Error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Error processing callback']);
        }
    }

    /**
     * Calculate transaction cost based on amount and transfer type
     */
    protected function calculateTransactionCost($amount, $isInternal, $network = 'mpesa')
    {
        $transferType = $isInternal ? 'internal' : 'external_' . $network;
        
        // Try to get from database first
        $cost = TransactionCost::where('transfer_type', $transferType)
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->first();

        if ($cost) {
            return $cost->fee;
        }

        // Fallback to default rates
        if ($isInternal) {
            // Internal transfers - lower fees
            if ($amount <= 100) return 2;
            if ($amount <= 500) return 5;
            if ($amount <= 1000) return 10;
            if ($amount <= 5000) return 15;
            return 20;
        } else {
            // External transfers - higher fees (similar to M-Pesa rates)
            if ($amount <= 100) return 7;
            if ($amount <= 500) return 13;
            if ($amount <= 1000) return 23;
            if ($amount <= 1500) return 28;
            if ($amount <= 2500) return 33;
            if ($amount <= 5000) return 38;
            if ($amount <= 7500) return 45;
            if ($amount <= 10000) return 50;
            if ($amount <= 15000) return 58;
            if ($amount <= 20000) return 65;
            if ($amount <= 35000) return 75;
            if ($amount <= 50000) return 85;
            return 105; 
        }
    }

    /**
     * Check for duplicate transfer
     */
    protected function checkDuplicateTransfer($senderWalletId, $recipientPhone, $amount)
    {
        $timeThreshold = 30; // 30 seconds
        $thresholdTime = Carbon::now()->subSeconds($timeThreshold);

        $lastTransfer = MoneyTransfer::where('sender_wallet_id', $senderWalletId)
            ->where('recipient_phone', $recipientPhone)
            ->where('amount', $amount)
            ->where('created_at', '>=', $thresholdTime)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastTransfer) {
            $timeDiff = Carbon::now()->diffInSeconds($lastTransfer->created_at);
            $waitTime = $timeThreshold - $timeDiff;

            return [
                'is_duplicate' => true,
                'wait_time' => $waitTime,
                'last_transaction_time' => $lastTransfer->created_at
            ];
        }

        return ['is_duplicate' => false];
    }

    /**
     * Record transaction revenue
     */
    protected function recordTransactionRevenue($amount, $transactionId, $type)
    {
        // This would typically go to a revenue/commission table
        // For now, we'll create a ledger entry for the platform
        Ledger::create([
            'wallet_id' => 'PLATFORM_REVENUE',
            'transaction_id' => $transactionId,
            'transaction_number' => $this->generateTransactionNumber(),
            'receipt_no' => Str::random(15),
            'description' => 'Transaction fee - ' . $type,
            'pay_method' => 'INTERNAL',
            'trans_id' => $transactionId,
            'amount' => $amount,
            'cr_dr' => 'cr',
            'trans_date' => Carbon::now(),
            'currency' => 'KES',
            'status' => true,
            'created_at' => Carbon::now()
        ]);
    }

    /**
     * Send internal transfer notifications
     */
    protected function sendInternalTransferNotifications($sender, $recipient, $amount, $fee, $transactionId)
    {
        $senderFirstName = explode(' ', $sender->fullname)[0];
        $recipientFirstName = explode(' ', $recipient->fullname)[0];

        // SMS to sender
        $senderMessage = view('sms.money_sent', [
            'senderName' => $senderFirstName,
            'recipientName' => $recipient->fullname,
            'recipientPhone' => $recipient->phone,
            'amount' => $amount,
            'fee' => $fee,
            'transactionId' => $transactionId,
            'balance' => $this->calculateBalance($sender->wallet_id),
            'timestamp' => Carbon::now()->format('d/m/Y H:i')
        ])->render();

        Outbox::create([
            'receiver' => $sender->phone,
            'message' => $senderMessage,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);

        // SMS to recipient
        $recipientMessage = view('sms.money_received', [
            'recipientName' => $recipientFirstName,
            'senderName' => $sender->fullname,
            'senderPhone' => $sender->phone,
            'amount' => $amount,
            'transactionId' => $transactionId,
            'balance' => $this->calculateBalance($recipient->wallet_id),
            'timestamp' => Carbon::now()->format('d/m/Y H:i')
        ])->render();

        Outbox::create([
            'receiver' => $recipient->phone,
            'message' => $recipientMessage,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);

        // Email notifications
        if ($sender->deriv_email) {
            Mail::to($sender->deriv_email)->send(
                new MoneyTransferSent($sender, $recipient, $amount, $fee, $transactionId)
            );
        }

        if ($recipient->deriv_email) {
            Mail::to($recipient->deriv_email)->send(
                new MoneyTransferReceived($recipient, $sender, $amount, $transactionId)
            );
        }
    }

    /**
     * Send external transfer notifications
     */
    protected function sendExternalTransferNotifications($sender, $recipientPhone, $amount, $fee, $transactionId, $recipientName = null)
    {
        $senderFirstName = explode(' ', $sender->fullname)[0];
        $recipientDisplay = $recipientName ? $recipientName . ' (' . $recipientPhone . ')' : $recipientPhone;

        // SMS to sender
        $senderMessage = view('sms.external_money_sent', [
            'senderName' => $senderFirstName,
            'recipientName' => $recipientDisplay,
            'amount' => $amount,
            'fee' => $fee,
            'transactionId' => $transactionId,
            'balance' => $this->calculateBalance($sender->wallet_id),
            'timestamp' => Carbon::now()->format('d/m/Y H:i')
        ])->render();

        Outbox::create([
            'receiver' => $sender->phone,
            'message' => $senderMessage,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);

        // Email notification
        if ($sender->deriv_email) {
            Mail::to($sender->deriv_email)->send(
                new MoneyTransferSent($sender, null, $amount, $fee, $transactionId, $recipientPhone, $recipientName)
            );
        }
    }

    /**
     * Send transfer completion notifications
     */
    protected function sendTransferCompletionNotifications($sender, $transfer)
    {
        $senderFirstName = explode(' ', $sender->fullname)[0];

        $message = view('sms.external_transfer_completed', [
            'senderName' => $senderFirstName,
            'recipientPhone' => $transfer->recipient_phone,
            'amount' => $transfer->amount,
            'mpesaCode' => $transfer->MpesaReceiptNumber,
            'transactionId' => $transfer->transaction_id,
            'timestamp' => Carbon::now()->format('d/m/Y H:i')
        ])->render();

        Outbox::create([
            'receiver' => $sender->phone,
            'message' => $message,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);
    }

    /**
     * Send transfer failure notifications
     */
    protected function sendTransferFailureNotifications($sender, $transfer)
    {
        $senderFirstName = explode(' ', $sender->fullname)[0];

        $message = view('sms.transfer_failed', [
            'senderName' => $senderFirstName,
            'recipientPhone' => $transfer->recipient_phone,
            'amount' => $transfer->amount,
            'fee' => $transfer->transaction_cost,
            'transactionId' => $transfer->transaction_id,
            'balance' => $this->calculateBalance($sender->wallet_id),
            'timestamp' => Carbon::now()->format('d/m/Y H:i')
        ])->render();

        Outbox::create([
            'receiver' => $sender->phone,
            'message' => $message,
            'created_on' => Carbon::now(),
            'status' => 'sent'
        ]);
    }
}