<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\{Invoice, Payment};
use Illuminate\Support\Facades\Http;

final class FpxGateway implements PaymentGateway, Refundable
{
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::Fpx;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return Http::withToken(config('services.fpx.key'))
            ->post(config('services.fpx.url').'/charge', [
                'invoice' => $invoice->number, 'amount' => $amount])
            ->throw()->json('reference');
    }

    public function refund(Payment $payment): void
    {
        Http::withToken(config('services.fpx.key'))
            ->post(config('services.fpx.url').'/refund', [
                'reference' => $payment->reference])
            ->throw();
    }

    // tiada autoDebit(): pelajar mesti log masuk bank setiap kali
}
