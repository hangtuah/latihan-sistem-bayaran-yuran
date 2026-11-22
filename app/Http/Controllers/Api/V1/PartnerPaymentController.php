<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PartnerPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        $payments = Payment::whereDate('paid_at', $data['date'])
            ->get(['reference', 'method', 'amount', 'paid_at']);

        return response()->json([
            'date' => $data['date'],
            'count' => $payments->count(),
            'total' => $payments->sum('amount'),
            'data' => $payments,
        ]);
    }
}
