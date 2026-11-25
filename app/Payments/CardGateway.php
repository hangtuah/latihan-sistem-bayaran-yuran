<?php

namespace App\Payments;

use App\Clients\CardClient;
use App\Enums\PaymentMethod;
use App\Models\Invoice;

final class CardGateway implements PaymentGateway
{
    /**
     * Create a new class instance.
     */
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::Card;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return (new CardClient(config('services.card.key')))
            ->charge($amount);
    }
}
