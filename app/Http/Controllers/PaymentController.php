<?php

namespace App\Http\Controllers;

use App\Data\PaymentData;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $service,
    ) {
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = $this->service->pay(
            $request->user()->student,
            PaymentData::fromRequest($request),
        );

        return redirect()
            ->route('payments.show', $payment);
    }

    public function show(Payment $payment)
    {
        return view('payments.show', [
            'payment' => $payment,
        ]);
    }
}
