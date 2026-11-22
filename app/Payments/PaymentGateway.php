<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\Invoice;

interface PaymentGateway
{
    public function supports(PaymentMethod $method): bool;

    public function charge(Invoice $invoice, float $amount): string;
}
