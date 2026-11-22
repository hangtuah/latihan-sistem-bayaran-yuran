<?php

use App\Billing\EarlyPaymentDiscount;
use App\Enums\FeeType;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

it('diskaun 2% untuk yuran semester yang dibayar awal', function (FeeType $type, string $today, float $expected) {
    $invoice = new Invoice(['type' => $type, 'amount' => 1100, 'due_date' => '2026-12-15']);

    expect((new EarlyPaymentDiscount)->for($invoice, Carbon::parse($today)))->toBe($expected);
})->with([
    'semester, 25 Nov'          => [FeeType::Semester, '2026-11-25', 22.0],
    'semester, 1 Dis 4 petang'  => [FeeType::Semester, '2026-12-01 16:00', 22.0],
    'semester, 2 Dis'           => [FeeType::Semester, '2026-12-02', 0.0],
    'asrama, 25 Nov'            => [FeeType::Hostel, '2026-11-25', 0.0],
]);
