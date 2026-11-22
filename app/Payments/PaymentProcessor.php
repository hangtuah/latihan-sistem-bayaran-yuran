<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use LogicException;

final class PaymentProcessor
{
    public function __construct(private iterable $gateways) {}

    public function charge(Invoice $invoice, PaymentMethod $method, float $amount): string
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->supports($method)) {
                return $gateway->charge($invoice, $amount);
            }
        }

        throw new LogicException("Kaedah bayaran tidak disokong: {$method->value}");
    }
}
