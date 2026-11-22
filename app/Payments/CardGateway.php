<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\{Invoice, Payment, Student};
use Illuminate\Support\Facades\Http;

final class CardGateway implements PaymentGateway, Refundable, SupportsAutoDebit
{
    public function supports(PaymentMethod $method): bool
    {
        return $method === PaymentMethod::Card;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return Http::withToken(config('services.card.key'))
            ->post(config('services.card.url').'/charge', [
                'invoice' => $invoice->number, 'amount' => $amount])
            ->throw()->json('reference');
    }

    public function refund(Payment $payment): void
    {
        Http::withToken(config('services.card.key'))
            ->post(config('services.card.url').'/refund', [
                'reference' => $payment->reference])
            ->throw();
    }

    // Hanya CardGateway ada method ini,
    // kerana hanya ia implement SupportsAutoDebit
    public function autoDebit(Student $student, Invoice $invoice): void
    {
        Http::withToken(config('services.card.key'))
            ->post(config('services.card.url').'/auto-debit', [
                'token'   => $student->card_token,
                'invoice' => $invoice->number,
                'amount'  => $invoice->amount,
            ])
            ->throw();
    }
}
