<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'transaction_number',
        'sender_wallet_id',
        'recipient_wallet_id',
        'recipient_phone',
        'recipient_name',
        'amount',
        'transaction_cost',
        'transfer_type',
        'status',
        'conversationID',
        'OriginatorConversationID',
        'MpesaReceiptNumber',
        'ResponseCode',
        'result_code',
        'result_desc',
        'transactionCompletedDateTime'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_cost' => 'decimal:2',
        'transactionCompletedDateTime' => 'datetime'
    ];

    public function sender()
    {
        return $this->belongsTo(Customer::class, 'sender_wallet_id', 'wallet_id');
    }

    public function recipient()
    {
        return $this->belongsTo(Customer::class, 'recipient_wallet_id', 'wallet_id');
    }

    public function isInternal()
    {
        return $this->transfer_type === 'internal';
    }

    public function isExternal()
    {
        return in_array($this->transfer_type, ['external_mpesa', 'external_airtel']);
    }
}