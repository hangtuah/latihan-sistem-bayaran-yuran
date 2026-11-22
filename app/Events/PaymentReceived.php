<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

final class PaymentReceived implements ShouldDispatchAfterCommit, ShouldBroadcast
{
    use Dispatchable;

    public function __construct(public Payment $payment) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('bursary')];
    }

    public function broadcastAs(): string
    {
        return 'payment.received';
    }

    /** Hanya medan yang dashboard perlukan; bukan seluruh model. */
    public function broadcastWith(): array
    {
        return [
            'invoice' => $this->payment->invoice->number,
            'amount' => $this->payment->amount,
            'method' => $this->payment->method->value,
        ];
    }
}
