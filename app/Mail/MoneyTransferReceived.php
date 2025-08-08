<?php


namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MoneyTransferReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $sender;
    public $amount;
    public $transactionId;

    public function __construct(Customer $recipient, Customer $sender, $amount, $transactionId)
    {
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
    }

    public function build()
    {
        return $this->subject('Money Received - KES ' . number_format($this->amount, 2))
                    ->view('emails.money_transfer_received');
    }
}
