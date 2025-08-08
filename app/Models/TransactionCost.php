<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_type',
        'min_amount',
        'max_amount',
        'fee',
        'is_active'
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public static function getFeeForAmount($amount, $transferType)
    {
        return self::where('transfer_type', $transferType)
            ->where('min_amount', '<=', $amount)
            ->where('max_amount', '>=', $amount)
            ->where('is_active', true)
            ->first()?->fee ?? 0;
    }
}