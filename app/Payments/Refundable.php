<?php

namespace App\Payments;

use App\Models\Payment;

interface Refundable
{
    public function refund(Payment $payment): void;
}
