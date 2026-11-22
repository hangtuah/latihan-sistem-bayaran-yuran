<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class PartnerPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        $day = Carbon::parse($data['date'], 'Asia/Kuala_Lumpur');

        $query = Payment::whereBetween('paid_at', [
            $day->copy()->startOfDay()->utc(),
            $day->copy()->endOfDay()->utc(),
        ]);

        $payments = (clone $query)->get(['reference', 'method', 'amount', 'paid_at']);

        return response()->json([
            'date' => $data['date'],
            'count' => $payments->count(),
            'total' => number_format((float) $query->sum('amount'), 2, '.', ''),
            'data' => $payments,
        ]);
    }
}
