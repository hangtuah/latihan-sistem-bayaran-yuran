<?php

namespace App\Data;

use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;

final readonly class PaymentData
{
    public function __construct(
        public int $invoiceId,
        public PaymentMethod $method,
    ) {}

    public static function fromRequest(StorePaymentRequest $r): self
    {
        return new self(
            invoiceId: $r->integer('invoice_id'),
            method:    $r->enum('method', PaymentMethod::class),
        );
    }
}
