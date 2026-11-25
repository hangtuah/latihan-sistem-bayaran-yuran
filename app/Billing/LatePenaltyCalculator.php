<?php

namespace App\Billing;

use App\Enums\FeeType;
use App\Models\Invoice;
use Carbon\CarbonInterface;

final class LatePenaltyCalculator
{
    /**
     * Create a new class instance.
     */
    public function for(Invoice $invoice, CarbonInterface $today): float
    {
        if ($today->lte($invoice->due_date)) {
            return 0.0;
        }

        return match ($invoice->type) {
            FeeType::Semester => (float) $invoice->amount * 0.05,
            FeeType::Hostel => (float) $invoice->amount * 0.02,
            FeeType::LibraryFine => 0.0,
        };
    }
}
