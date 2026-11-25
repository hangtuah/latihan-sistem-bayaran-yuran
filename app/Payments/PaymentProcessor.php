<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use LogicException;

class PaymentProcessor
{
    /**
     * Create a new class instance.
     */
    public function __construct(private array $gateways)
    {
        //
    }

    public function charge(
        PaymentMethod $method,
        Invoice       $invoice,
        float         $amount,
    ): string
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->supports($method)) {
                return $gateway->charge($invoice, $amount);
            }
        }

        throw new LogicException(
            "Tiada payment gateway untuk kaedah {$method->value}."
        );
    }
}
