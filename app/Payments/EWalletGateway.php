<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\{Invoice, Payment};
use Illuminate\Support\Facades\Http;

final class EWalletGateway implements PaymentGateway, Refundable
{
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::EWallet;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return Http::withToken(config('services.ewallet.key'))
            ->post(config('services.ewallet.url').'/charge', [
                'invoice' => $invoice->number, 'amount' => $amount])
            ->throw()->json('reference');
    }

    public function refund(Payment $payment): void
    {
        Http::withToken(config('services.ewallet.key'))
            ->post(config('services.ewallet.url').'/refund', [
                'reference' => $payment->reference])
            ->throw();
    }

    // tiada autoDebit(): e-wallet tak sokong
}
