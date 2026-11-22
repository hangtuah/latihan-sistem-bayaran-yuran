<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Support\Facades\Gate;

final class InvoiceController extends Controller
{
    public function show(Invoice $invoice): InvoiceResource
    {
        Gate::authorize('view', $invoice);

        return new InvoiceResource($invoice);
    }
}
