<?php

namespace App\Console\Commands;

use App\Data\PaymentData;
use App\Enums\PaymentMethod;
use App\Exceptions\InvoiceAlreadyPaidException;
use App\Models\Student;
use App\Services\PaymentService;
use Illuminate\Console\Command;

final class ImportCounterPayments extends Command
{
    protected $signature = 'payments:import {file}';

    protected $description = 'Import penyata kaunter Bendahari (matrik,invois)';

    public function handle(PaymentService $service): void
    {
        foreach (file($this->argument('file')) as $line) {
            [$matric, $invoiceId] = str_getcsv($line);

            try {
                $service->pay(
                    Student::where('matric', $matric)->firstOrFail(),
                    new PaymentData((int) $invoiceId, PaymentMethod::Counter),
                );
            } catch (InvoiceAlreadyPaidException $e) {
                $this->warn($e->getMessage());
            }
        }
    }
}
