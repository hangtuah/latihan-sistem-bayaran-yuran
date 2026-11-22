<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\Invoice;

// Pelajar bayar tunai di kaunter Pejabat Bendahari.
// Tiada API bank: rujukan ialah nombor resit kaunter.
final class CounterGateway implements PaymentGateway
{
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::Counter;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return 'KTR-'.$invoice->number;
    }
}
