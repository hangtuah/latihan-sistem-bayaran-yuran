<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

final class ReceiptController extends Controller
{
    public function show(Payment $payment): JsonResponse
    {
        return response()->json([
            'reference' => $payment->reference,
            'student' => $payment->student->name,
            'invoice' => $payment->invoice->number,
            'amount' => $payment->amount,
        ]);
    }

    public function today(): JsonResponse
    {
        $payments = Payment::whereDate('paid_at', today())->get();

        return response()->json($payments->map(fn (Payment $p) => [
            'reference' => $p->reference,
            'student' => $p->student->name,
            'amount' => $p->amount,
        ]));
    }
}
