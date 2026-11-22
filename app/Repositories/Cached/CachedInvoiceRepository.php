<?php

namespace App\Repositories\Cached;

use App\Models\{Invoice, Payment, Student};
use App\Repositories\Contracts\InvoiceRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;

final class CachedInvoiceRepository implements InvoiceRepository
{
    public function __construct(private InvoiceRepository $inner, private Cache $cache) {}

    public function findOrFail(int $id): Invoice
    {
        return $this->inner->findOrFail($id);
    }

    public function outstandingFor(Student $student): Collection
    {
        return $this->cache->remember(
            "invoices:outstanding:{$student->id}",
            now()->addMinutes(10),
            fn () => $this->inner->outstandingFor($student),
        );
    }

    public function recordPayment(Invoice $invoice, array $attributes): Payment
    {
        $payment = $this->inner->recordPayment($invoice, $attributes);
        $this->cache->forget("invoices:outstanding:{$invoice->student_id}");

        return $payment;
    }

    public function overdue(): Collection
    {
        return $this->inner->overdue();
    }

    public function overdueSummary(): array
    {
        return $this->inner->overdueSummary();
    }
}
