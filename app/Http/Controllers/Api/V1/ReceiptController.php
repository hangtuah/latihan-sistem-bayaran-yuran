<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

final class ReceiptController extends Controller
{
    public function show(Payment $payment): JsonResponse
    {
        Gate::authorize('view', $payment->invoice);

        return response()->json([
            'reference' => $payment->reference,
            'student' => $payment->student->name,
            'invoice' => $payment->invoice->number,
            'amount' => $payment->amount,
        ]);
    }

    public function today(): JsonResponse
    {
        $day = now('Asia/Kuala_Lumpur');

        $payments = Payment::with('student')
            ->whereBetween('paid_at', [
                $day->copy()->startOfDay()->utc(),
                $day->copy()->endOfDay()->utc(),
            ])
            ->get();

        return response()->json($payments->map(fn (Payment $p) => [
            'reference' => $p->reference,
            'student' => $p->student->name,
            'amount' => $p->amount,
        ]));
    }
}
