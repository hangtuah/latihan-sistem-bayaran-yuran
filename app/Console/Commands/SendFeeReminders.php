<?php

namespace App\Console\Commands;

use App\Mail\FeeReminder;
use App\Models\Invoice;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('invoices:send-reminders')]
#[Description('Hantar peringatan untuk invois yang lewat 7 hari')]
final class SendFeeReminders extends Command
{
    public function handle(): int
    {
        $invoices = Invoice::overdue()
            ->where('due_date', today('Asia/Kuala_Lumpur')->subDays(7)->toDateString())
            ->with('student');

        $count = 0;
        $invoices->lazyById(500)->each(function (Invoice $invoice) use (&$count) {
            Mail::to($invoice->student->email)->queue(new FeeReminder($invoice));
            $count++;
        });

        $this->info("{$count} peringatan dimasukkan ke queue.");

        return self::SUCCESS;
    }
}
