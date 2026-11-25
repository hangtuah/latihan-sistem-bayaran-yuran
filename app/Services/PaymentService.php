<?php

namespace App\Services;

use App\Billing\LatePenaltyCalculator;
use App\Data\PaymentData;
use App\Events\PaymentReceived;
use App\Exceptions\InvoiceAlreadyPaidException;
use App\Models\Payment;
use App\Models\Student;
use App\Payments\PaymentProcessor;
use App\Repositories\Contracts\InvoiceRepository;
use Illuminate\Support\Facades\DB;

final class PaymentService
{
    public function __construct(
        private InvoiceRepository $invoices,
        private LatePenaltyCalculator $penalties,
        private PaymentProcessor $processor,
    ) {
    }

    public function pay(Student $student, PaymentData $data): Payment
    {
        $invoice = $this->invoices->findOrFail($data->invoiceId);

        if ($invoice->paid_at !== null) {
            throw InvoiceAlreadyPaidException::for($invoice);
        }

        $amount = (float) $invoice->amount
            + $this->penalties->for($invoice, now());

        // Do not keep a database transaction open while waiting for the bank.
        $reference = $this->processor->charge(
            $data->method,
            $invoice,
            $amount,
        );

        return DB::transaction(function () use (
            $invoice,
            $student,
            $data,
            $amount,
            $reference,
        ): Payment {
            $payment = $this->invoices->recordPayment($invoice, [
                'student_id' => $student->id,
                'method' => $data->method,
                'amount' => $amount,
                'reference' => $reference,
                'paid_at' => now(),
            ]);

            PaymentReceived::dispatch($payment);

            return $payment;
        });
    }
}
