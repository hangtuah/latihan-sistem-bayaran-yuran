<?php

namespace App\Console\Commands;

use App\Data\PaymentData;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Payments\PaymentGateway;
use App\Payments\PaymentProcessor;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Throwable;

// Lab Modul 2: simulasi pelajar klik "Bayar" dua kali.
// Dua proses PHP memanggil PaymentService::pay() untuk invois yang sama
// pada masa yang sama. Gateway palsu tidur 0.6 saat, seperti bank sebenar.
final class LabDoubleClick extends Command
{
    protected $signature = 'lab:double-click {invoice} {--klik=}';

    protected $description = 'Lab Modul 2: dua klik serentak untuk invois yang sama';

    public function handle(): int
    {
        return $this->option('klik') ? $this->click() : $this->race();
    }

    private function race(): int
    {
        $invoice = Invoice::findOrFail($this->argument('invoice'));
        $invoice->payments()->delete();
        $invoice->update(['paid_at' => null]);

        $processes = collect([1, 2])->map(fn (int $n) => tap(new Process([
            PHP_BINARY, base_path('artisan'), 'lab:double-click', $invoice->id, "--klik={$n}",
        ], base_path()))->start());

        // cetak output kedua-dua proses mengikut masa ia sampai
        while ($processes->contains(fn (Process $p) => $p->isRunning())) {
            $processes->each(fn (Process $p) => $this->output->write($p->getIncrementalOutput().$p->getIncrementalErrorOutput()));
            usleep(20_000);
        }
        $processes->each(fn (Process $p) => $this->output->write($p->getIncrementalOutput().$p->getIncrementalErrorOutput()));

        $this->newLine();
        $this->line("payments untuk invois {$invoice->id}: ".Payment::where('invoice_id', $invoice->id)->count());

        return self::SUCCESS;
    }

    private function click(): int
    {
        $name = 'klik'.$this->option('klik');

        app()->instance(PaymentProcessor::class, new PaymentProcessor([
            new class($name) implements PaymentGateway
            {
                public function __construct(private string $name) {}

                public function supports(PaymentMethod $method): bool
                {
                    return true;
                }

                public function charge(Invoice $invoice, float $amount): string
                {
                    usleep(600_000);
                    echo "{$this->name}: bank caj RM".number_format($amount, 2).PHP_EOL;

                    return 'LAB'.random_int(100000, 999999);
                }
            },
        ]));

        $invoice = Invoice::findOrFail($this->argument('invoice'));

        try {
            app(PaymentService::class)->pay($invoice->student, new PaymentData($invoice->id, PaymentMethod::Fpx));
            $this->line("{$name}: bayaran direkod");
        } catch (Throwable $e) {
            $this->line("{$name}: ".class_basename($e).': '.$e->getMessage());
        }

        return self::SUCCESS;
    }
}
