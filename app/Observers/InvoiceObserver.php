<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

final class InvoiceObserver
{
    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('amount')) {
            Log::warning('Amaun invois diubah', [
                'invoice' => $invoice->number,
                'dari' => $invoice->getOriginal('amount'),
                'kepada' => $invoice->amount,
            ]);
        }
    }
}
