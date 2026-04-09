<?php

namespace App\Mail;

use App\Models\RentalOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RentalOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public RentalOrder $order)
    {
    }

    public function build()
    {
        return $this
            ->subject('Bestätigung Vermietung #'.$this->order->id)
            ->view('emails.rental-order-confirmation');
    }
}
