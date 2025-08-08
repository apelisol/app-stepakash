<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $fillable = [
        'wallet_id',
        'transaction_id',
        'transaction_number',
        'receipt_no',
        'description',
        'pay_method',
        'trans_id',
        'amount',
        'cr_dr',
        'trans_date',
        'currency',
        'rate',
        'charge_percent',
        'charge',
        'total_amount',
        'status',
        'deriv_account',
        'created_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'rate' => 'float',
        'charge_percent' => 'float',
        'charge' => 'float',
        'total_amount' => 'float',
        'status' => 'boolean',
        'deriv_account' => 'boolean',
        'trans_date' => 'datetime',
        'created_at' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'wallet_id', 'wallet_id');
    }
}