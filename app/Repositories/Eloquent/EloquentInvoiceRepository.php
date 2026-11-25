<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Repositories\Contracts\InvoiceRepository;
use Illuminate\Database\Eloquent\Collection;

final class EloquentInvoiceRepository implements InvoiceRepository
{
    public function findOrFail(int $id): Invoice
    {
        return Invoice::findOrFail($id);
    }

    public function outstandingFor(Student $student): Collection
    {
        return $student->invoices()
            ->outstanding()
            ->orderBy('due_date')
            ->get();
    }

    public function overdue(): Collection
    {
        return Invoice::with('student.programme')
            ->overdue()
            ->orderBy('due_date')
            ->limit(50)
            ->get();
    }

    public function recordPayment(Invoice $invoice, array $attributes): Payment
    {
        $payment = $invoice->payments()->create($attributes);
        $invoice->update(['paid_at' => $payment->paid_at]);

        return $payment;
    }
}
