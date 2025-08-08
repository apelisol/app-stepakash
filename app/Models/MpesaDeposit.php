<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaDeposit extends Model
{
    protected $fillable = [
        'wallet_id',
        'phone',
        'amount',
        'MerchantRequestID',
        'CheckoutRequestID',
        'txn',
        'TransactionDate',
        'paid',
        'result_code',
        'result_desc',
        'created_on',
        'updated_on'
    ];

    protected $casts = [
        'paid' => 'boolean',
        'amount' => 'float',
        'created_on' => 'datetime',
        'updated_on' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'wallet_id', 'wallet_id');
    }
}