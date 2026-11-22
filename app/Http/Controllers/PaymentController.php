<?php

namespace App\Http\Controllers;

use App\Data\PaymentData;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $service,
    ) {}

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = $this->service->pay(
            $request->user()->student,
            PaymentData::fromRequest($request),
        );

        return to_route('payments.show', $payment)
            ->with('status', 'Bayaran berjaya.');
    }

    public function show(Payment $payment): View
    {
        return view('payments.show', ['payment' => $payment]);
    }
}
