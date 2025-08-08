<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MpesaDeposit;
use App\Models\MpesaWithdrawal;
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
use Illuminate\Support\Facades\Mail;
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
     * Calculate customer balance
     */
    protected function calculateBalance($walletId)
    {
        $credits = Ledger::where('wallet_id', $walletId)
            ->where('cr_dr', 'cr')
            ->where('status', true)
            ->sum('amount');

        $debits = Ledger::where('wallet_id', $walletId)
            ->where('cr_dr', 'dr')
            ->where('status', true)
            ->sum('amount');

        return $credits - $debits;
    }

    /**
     * Credit customer wallet
     */
    protected function creditWallet($walletId, $amount, $reference, $description)
    {
        try {
            $transactionId = Str::random(9);
            $transactionNumber = $this->generateTransactionNumber();
            $receiptNo = Str::random(15);

            Ledger::create([
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

            return true;
        } catch (\Exception $e) {
            Log::error('Credit Wallet Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Debit customer wallet
     */
    protected function debitWallet($walletId, $amount, $transactionId, $transactionNumber, $description)
    {
        try {
            $receiptNo = Str::random(15);

            Ledger::create([
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

            return true;
        } catch (\Exception $e) {
            Log::error('Debit Wallet Error: ' . $e->getMessage());
            return false;
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
}