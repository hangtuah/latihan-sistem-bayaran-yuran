<?php

namespace App\Repositories\Contracts;

use App\Models\{Invoice, Payment, Student};
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepository
{
    public function findOrFail(int $id): Invoice;

    /** Invois belum bayar, tarikh akhir paling awal dahulu. */
    public function outstandingFor(Student $student): Collection;

    /** Rekod bayaran dan tanda invois berbayar. */
    public function recordPayment(Invoice $invoice, array $attributes): Payment;

    /** 50 invois belum bayar yang paling lama lewat. */
    public function overdue(): Collection;

    /** @return array{total_count: int, total_amount: string} */
    public function overdueSummary(): array;
}
