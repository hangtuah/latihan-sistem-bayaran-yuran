<?php

namespace App\Data;

use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;

final readonly class PaymentData
{
    public function __construct(
        public int $invoiceId,
        public PaymentMethod $method,
    ) {
    }

    public static function fromRequest(StorePaymentRequest $request): self
    {
        return new self(
            invoiceId: $request->integer('invoice_id'),
            method: $request->enum('method', PaymentMethod::class),
        );
    }
}
