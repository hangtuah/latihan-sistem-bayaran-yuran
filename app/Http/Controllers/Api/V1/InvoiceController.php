<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class InvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $invoices = Invoice::where(
            'student_id',
            $request->user()->student_id,
        )
            ->outstanding()
            ->orderBy('due_date')
            ->paginate(10);

        return InvoiceResource::collection($invoices);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice);
    }
}
