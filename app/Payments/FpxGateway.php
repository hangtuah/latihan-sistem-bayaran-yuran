<?php

namespace App\Payments;

use App\Clients\FpxClient;
use App\Enums\PaymentMethod;
use App\Models\Invoice;

final class FpxGateway implements PaymentGateway
{
    /**
     * Create a new class instance.
     */
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::Fpx;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return (new FpxClient(config('services.fpx.key')))
            ->pay($invoice->number, $amount);
    }
}
