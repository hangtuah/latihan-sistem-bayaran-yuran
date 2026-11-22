<?php

namespace App\Repositories\Cached;

use App\Models\{Invoice, Payment, Student};
use App\Repositories\Contracts\InvoiceRepository;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

        // padam selepas commit: request lain tidak sempat cache data lama
        DB::afterCommit(function () use ($invoice) {
            $this->cache->forget("invoices:outstanding:{$invoice->student_id}");
            $this->cache->forget('invoices:overdue-summary');
        });

        return $payment;
    }

    public function overdue(): Collection
    {
        return $this->inner->overdue(); // senarai berubah setiap bayaran: tidak di-cache
    }

    public function overdueSummary(): array
    {
        // segar 5 minit; 5 - 15 minit pulangkan nilai lama sambil kira semula di belakang
        return $this->cache->flexible(
            'invoices:overdue-summary',
            [300, 900],
            fn () => $this->inner->overdueSummary(),
        );
    }
}
