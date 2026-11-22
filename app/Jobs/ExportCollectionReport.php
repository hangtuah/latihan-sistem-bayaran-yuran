<?php

namespace App\Jobs;

use App\Mail\CollectionReportReady;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

final class ExportCollectionReport implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public function __construct(public User $requestedBy, public int $year) {}

    /** Satu laporan untuk satu tahun dalam satu masa. */
    public function uniqueId(): string
    {
        return (string) $this->year;
    }

    public function handle(): void
    {
        $path = "reports/kutipan-{$this->year}.csv";
        Storage::makeDirectory('reports');
        $handle = fopen(Storage::path($path), 'w');
        fputcsv($handle, ['rujukan', 'invois', 'kaedah', 'amaun', 'tarikh']);

        Payment::with('invoice')->whereYear('paid_at', $this->year)->lazyById(1000)
            ->each(fn (Payment $p) => fputcsv($handle, [
                $p->reference, $p->invoice->number, $p->method->value, $p->amount, $p->paid_at,
            ]));
        fclose($handle);

        Mail::to($this->requestedBy->email)->send(new CollectionReportReady($path));
    }
}
