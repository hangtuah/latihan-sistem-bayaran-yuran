<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'method' => $this->method,
            'amount' => $this->amount,
            'reference' => $this->reference,
            'paid_at' => $this->paid_at->toIso8601String(),
        ];
    }
}
