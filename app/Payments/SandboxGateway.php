<?php

namespace App\Payments;

use App\Enums\PaymentMethod;
use App\Models\Invoice;

// Digunakan di semua persekitaran selain production (lihat AppServiceProvider).
// Tidak menghubungi bank; pulangkan rujukan palsu SBX######.
final class SandboxGateway implements PaymentGateway
{
    public function supports(PaymentMethod $method): bool
    {
        return true;
    }

    public function charge(Invoice $invoice, float $amount): string
    {
        return 'SBX'.random_int(100000, 999999);
    }
}
