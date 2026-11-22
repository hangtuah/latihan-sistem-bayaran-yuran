<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'amount' => ['sen' => (int) round($this->amount * 100), 'currency' => 'MYR'],
            'due_date' => $this->due_date->toDateString(),
            'status' => $this->paid_at ? 'paid'
                : ($this->due_date->isPast() ? 'overdue' : 'outstanding'),
        ];
    }
}
