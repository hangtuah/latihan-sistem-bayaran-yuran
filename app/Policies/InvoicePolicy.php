<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

final class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->isBursary()
            || $user->student_id === $invoice->student_id;
    }

    public function pay(User $user, Invoice $invoice): bool
    {
        return $user->student_id === $invoice->student_id;
    }
}
