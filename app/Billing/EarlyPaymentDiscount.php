<?php

namespace App\Billing;

use App\Enums\FeeType;
use App\Models\Invoice;
use Carbon\CarbonInterface;

// Lab 3 Modul 5 (TDD): yuran semester yang dibayar sekurang-kurangnya
// 14 hari sebelum tarikh akhir dapat diskaun 2%.
final class EarlyPaymentDiscount
{
    private const RATE = 0.02;

    private const DAYS_BEFORE = 14;

    public function for(Invoice $invoice, CarbonInterface $today): float
    {
        $earlyEnough = $today->lte($invoice->due_date->subDays(self::DAYS_BEFORE)->endOfDay());

        if ($invoice->type !== FeeType::Semester || ! $earlyEnough) {
            return 0.0;
        }

        return round($invoice->amount * self::RATE, 2);
    }
}
