<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SyncPaymentToFinance implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public array $backoff = [10, 30, 60];

    public function __construct(public Payment $payment) {}

    public function handle(): void
    {
        Http::timeout(5)
            ->post(config('services.finance.url').'/payments', [
                'reference' => $this->payment->reference,
                'amount' => $this->payment->amount,
            ])
            ->throw();

        Log::channel('payments')->info('Disalin ke sistem kewangan', [
            'reference' => $this->payment->reference,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::critical('Bayaran gagal dihantar ke sistem kewangan', [
            'reference' => $this->payment->reference,
            'error' => $exception?->getMessage(),
        ]);
    }
}
