<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $payment;

    /**
     * Create a new event instance.
     */
    public function __construct($booking, $payment = null)
    {
        $this->booking = $booking;
        $this->payment = $payment;
    }
}