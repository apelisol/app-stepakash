<?php

// Email Mailable - app/Mail/MoneyTransferSent.php
namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MoneyTransferSent extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $recipient;
    public $amount;
    public $fee;
    public $transactionId;
    public $recipientPhone;
    public $recipientName;
    public $isExternal;

    public function __construct(Customer $sender, $recipient, $amount, $fee, $transactionId, $recipientPhone = null, $recipientName = null)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->transactionId = $transactionId;
        $this->recipientPhone = $recipientPhone;
        $this->recipientName = $recipientName;
        $this->isExternal = is_null($recipient);
    }

    public function build()
    {
        return $this->subject('Money Transfer Confirmation - KES ' . number_format($this->amount, 2))
                    ->view('emails.money_transfer_sent');
    }
}


?>