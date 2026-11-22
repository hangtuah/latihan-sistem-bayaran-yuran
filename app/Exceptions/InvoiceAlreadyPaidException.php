<?php

namespace App\Exceptions;

use App\Models\Invoice;
use DomainException;
use Illuminate\Http\Request;

final class InvoiceAlreadyPaidException extends DomainException
{
    public static function for(Invoice $invoice): self
    {
        return new self(sprintf(
            'Invois %s sudah dibayar pada %s.',
            $invoice->number, $invoice->paid_at->format('d/m/Y H:i'),
        ));
    }

    // Laravel panggil method ini secara automatik bila exception tidak ditangkap
    public function render(Request $request)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $this->getMessage()], 409)
            : back()->withInput()->withErrors(['invoice_id' => $this->getMessage()]);
    }
}
