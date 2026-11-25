<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
interface InvoiceRepository
{
    public function findOrFail(int $id): Invoice;

    /**
     * Retrieve unpaid invoices ordered by the earliest due date.
     */
    public function outstandingFor(Student $student): Collection;

    /**
     * Record a payment and mark the invoice as paid.
     *
     * @param array<string, mixed> $attributes
     */
    public function recordPayment(
        Invoice $invoice,
        array $attributes,
    ): Payment;
}
