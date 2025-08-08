<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $customer;
    public $otp;

    /**
     * Create a new message instance.
     *
     * @param Customer $customer
     * @param string $otp
     */
    public function __construct(Customer $customer, string $otp)
    {
        $this->customer = $customer;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your StepaKash Password Reset OTP')
            ->view('emails.password_reset')
            ->with([
                'customer' => $this->customer,
                'otp' => $this->otp
            ]);
    }
}
