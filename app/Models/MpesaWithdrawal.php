<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaWithdrawal extends Model
{
    protected $fillable = [
        'transaction_id',
        'transaction_number',
        'wallet_id',
        'cr_number',
        'amount',
        'phone',
        'conversationID',
        'OriginatorConversationID',
        'ResponseCode',
        'MpesaReceiptNumber',
        'receiverPartyPublicName',
        'transactionCompletedDateTime',
        'b2cUtilityAccountAvailableFunds',
        'b2cWorkingAccountAvailableFunds',
        'withdraw',
        'paid',
        'result_code',
        'result_desc',
        'request_date',
        'updated_on'
    ];

    protected $casts = [
        'withdraw' => 'boolean',
        'paid' => 'boolean',
        'amount' => 'float',
        'request_date' => 'datetime',
        'updated_on' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'wallet_id', 'wallet_id');
    }
}