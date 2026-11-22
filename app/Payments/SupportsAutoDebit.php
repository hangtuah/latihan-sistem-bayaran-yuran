<?php

namespace App\Payments;

use App\Models\Invoice;
use App\Models\Student;

interface SupportsAutoDebit
{
    public function autoDebit(Student $student, Invoice $invoice): void;
}
