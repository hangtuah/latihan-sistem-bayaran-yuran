<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepository
{
    public function findOrFail(int $id): Invoice;

    /** Invois belum bayar, tarikh akhir paling awal dahulu. */
    public function outstandingFor(Student $student): Collection;

    /** Invois tertunggak bersama maklumat pelajar dan program. */
    public function overdue(): Collection;

    /** Rekod bayaran dan tanda invois berbayar. */
    public function recordPayment(Invoice $invoice, array $attributes): Payment;
}
