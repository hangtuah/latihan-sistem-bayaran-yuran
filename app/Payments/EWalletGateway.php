<?php

namespace App\Payments;

use App\Clients\EWalletClient;
use App\Enums\PaymentMethod;
use App\Models\Invoice;

final class EWalletGateway implements PaymentGateway
{
    /**
     * Create a new class instance.
     */
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::EWallet;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return (new EWalletClient(config('services.ewallet.key')))
            ->debit($amount);
    }
}
