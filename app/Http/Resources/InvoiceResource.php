<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'number'     => $this->number,
            'type'       => $this->type,
            'amount'     => $this->amount,
            'due_date'   => $this->due_date->toDateString(),
            'paid_at'    => $this->paid_at?->toIso8601String(),
            'is_overdue' => $this->paid_at === null
                && $this->due_date->isPast(),
        ];
    }
}
