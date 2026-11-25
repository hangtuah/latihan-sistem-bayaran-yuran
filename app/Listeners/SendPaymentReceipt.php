<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Mail\PaymentReceipt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

final class SendPaymentReceipt implements ShouldQueue
{
    public function handle(PaymentReceived $event): void
    {
        Mail::to($event->payment->student->email)
            ->send(new PaymentReceipt($event->payment));
    }
}
