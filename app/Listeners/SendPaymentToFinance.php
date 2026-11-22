<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Jobs\SyncPaymentToFinance;

final class SendPaymentToFinance
{
    public function handle(PaymentReceived $event): void
    {
        SyncPaymentToFinance::dispatch($event->payment);
    }
}
