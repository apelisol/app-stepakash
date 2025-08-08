<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionCost;

class TransactionCostSeeder extends Seeder
{
    public function run()
    {
        $costs = [
            // Internal transfer rates
            ['transfer_type' => 'internal', 'min_amount' => 1, 'max_amount' => 100, 'fee' => 2],
            ['transfer_type' => 'internal', 'min_amount' => 101, 'max_amount' => 500, 'fee' => 5],
            ['transfer_type' => 'internal', 'min_amount' => 501, 'max_amount' => 1000, 'fee' => 10],
            ['transfer_type' => 'internal', 'min_amount' => 1001, 'max_amount' => 5000, 'fee' => 15],
            ['transfer_type' => 'internal', 'min_amount' => 5001, 'max_amount' => 999999, 'fee' => 20],

            // External M-Pesa rates
            ['transfer_type' => 'external_mpesa', 'min_amount' => 1, 'max_amount' => 100, 'fee' => 7],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 101, 'max_amount' => 500, 'fee' => 13],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 501, 'max_amount' => 1000, 'fee' => 23],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 1001, 'max_amount' => 1500, 'fee' => 28],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 1501, 'max_amount' => 2500, 'fee' => 33],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 2501, 'max_amount' => 5000, 'fee' => 38],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 5001, 'max_amount' => 7500, 'fee' => 45],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 7501, 'max_amount' => 10000, 'fee' => 50],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 10001, 'max_amount' => 15000, 'fee' => 58],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 15001, 'max_amount' => 20000, 'fee' => 65],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 20001, 'max_amount' => 35000, 'fee' => 75],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 35001, 'max_amount' => 50000, 'fee' => 85],
            ['transfer_type' => 'external_mpesa', 'min_amount' => 50001, 'max_amount' => 999999, 'fee' => 105],

            // External Airtel rates
            ['transfer_type' => 'external_airtel', 'min_amount' => 1, 'max_amount' => 100, 'fee' => 7],
            ['transfer_type' => 'external_airtel', 'min_amount' => 101, 'max_amount' => 500, 'fee' => 13],
            ['transfer_type' => 'external_airtel', 'min_amount' => 501, 'max_amount' => 1000, 'fee' => 23],
            ['transfer_type' => 'external_airtel', 'min_amount' => 1001, 'max_amount' => 1500, 'fee' => 28],
            ['transfer_type' => 'external_airtel', 'min_amount' => 1501, 'max_amount' => 2500, 'fee' => 33],
            ['transfer_type' => 'external_airtel', 'min_amount' => 2501, 'max_amount' => 5000, 'fee' => 38],
            ['transfer_type' => 'external_airtel', 'min_amount' => 5001, 'max_amount' => 7500, 'fee' => 45],
            ['transfer_type' => 'external_airtel', 'min_amount' => 7501, 'max_amount' => 10000, 'fee' => 50],
            ['transfer_type' => 'external_airtel', 'min_amount' => 10001, 'max_amount' => 15000, 'fee' => 58],
            ['transfer_type' => 'external_airtel', 'min_amount' => 15001, 'max_amount' => 20000, 'fee' => 65],
            ['transfer_type' => 'external_airtel', 'min_amount' => 20001, 'max_amount' => 35000, 'fee' => 75],
            ['transfer_type' => 'external_airtel', 'min_amount' => 35001, 'max_amount' => 50000, 'fee' => 85],
            ['transfer_type' => 'external_airtel', 'min_amount' => 50001, 'max_amount' => 999999, 'fee' => 105],
        ];

        foreach ($costs as $cost) {
            TransactionCost::create($cost);
        }
    }
}