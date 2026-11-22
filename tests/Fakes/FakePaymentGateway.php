<?php

namespace Tests\Fakes;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Payments\PaymentGateway;

final class FakePaymentGateway implements PaymentGateway
{
    public array $charges = [];

    public function supports(PaymentMethod $method): bool
    {
        return true;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        $this->charges[] = compact('invoice', 'amount');

        return 'FAKE-'.count($this->charges);
    }
}
