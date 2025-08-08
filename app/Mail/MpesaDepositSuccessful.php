<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MpesaDepositSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $amount;
    public $mpesaCode;
    public $balance;
    public $dashboardUrl;

    public function __construct(Customer $customer, $amount, $mpesaCode, $balance, $dashboardUrl)
    {
        $this->customer = $customer;
        $this->amount = $amount;
        $this->mpesaCode = $mpesaCode;
        $this->balance = $balance;
        $this->dashboardUrl = $dashboardUrl;
    }

    public function build()
    {
        return $this->subject('M-Pesa Deposit Received')
            ->view('emails.deposit_successful');
    }
}