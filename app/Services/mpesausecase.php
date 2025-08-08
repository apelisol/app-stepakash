<?php
use App\Services\MpesaService;

$mpesa = new MpesaService();
$balance = $mpesa->checkAccountBalance(route('mpesa.balance.callback'));

// Buy airtime
$airtime = $mpesa->buyAirtime('254712345678', 100, route('mpesa.airtime.callback'));

// Check transaction status
$status = $mpesa->checkTransactionStatus('ABC123XYZ', route('mpesa.status.callback'));

// Reverse a transaction
$reversal = $mpesa->reverseTransaction('ABC123XYZ', 100, route('mpesa.reversal.callback'));

$funding =$mpesa->checkAccountBalance('254703416091', 100, route('mpesa.balance.callback'));