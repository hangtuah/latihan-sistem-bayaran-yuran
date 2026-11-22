<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;

final class PaymentReceived implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(public Payment $payment) {}
}
