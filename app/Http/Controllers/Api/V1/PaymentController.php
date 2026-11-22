<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\PaymentData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

final class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $service,
    ) {}

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->service->pay(
            $request->user()->student,
            PaymentData::fromRequest($request),
        );

        return PaymentResource::make($payment)
            ->response()
            ->setStatusCode(201);
    }
}
