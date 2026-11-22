<?php

namespace App\Billing;

use App\Enums\FeeType;
use App\Models\Invoice;
use Carbon\CarbonInterface;

final class LatePenaltyCalculator
{
    private const MAX_PENALTY = 200.0;

    public function for(Invoice $invoice, CarbonInterface $today): float
    {
        if ($today->lte($invoice->due_date->endOfDay())) {
            return 0.0;
        }

        $rate = match ($invoice->type) {
            FeeType::Semester    => 0.05,
            FeeType::Hostel      => 0.02,
            FeeType::LibraryFine => 0.0,
        };

        return min(round($invoice->amount * $rate, 2), self::MAX_PENALTY);
    }
}
