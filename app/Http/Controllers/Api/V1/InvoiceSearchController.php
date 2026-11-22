<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class InvoiceSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $number = $request->query('number');
        $invoices = Invoice::whereRaw("number = '$number'")->get(['number', 'amount']);

        return response()->json(['data' => $invoices]);
    }
}
