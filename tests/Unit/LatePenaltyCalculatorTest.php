<?php

use App\Billing\LatePenaltyCalculator;
use App\Enums\FeeType;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class);

function invoice(FeeType $type, float $amount): Invoice
{
    return new Invoice(['type' => $type, 'amount' => $amount, 'due_date' => '2026-12-15']);
}

it('tiada penalti sebelum tarikh akhir', function () {
    $penalty = (new LatePenaltyCalculator)
        ->for(invoice(FeeType::Semester, 1100), Carbon::parse('2026-12-01'));

    expect($penalty)->toBe(0.0);
});

it('kira penalti ikut jenis yuran', function (FeeType $type, float $amount, float $expected) {
    $penalty = (new LatePenaltyCalculator)
        ->for(invoice($type, $amount), Carbon::parse('2026-12-20'));

    expect($penalty)->toBe($expected);
})->with([
    'semester 5%'          => [FeeType::Semester, 1100, 55.0],
    'asrama 2%'            => [FeeType::Hostel, 920, 18.4],
    'denda perpustakaan 0' => [FeeType::LibraryFine, 30, 0.0],
]);

it('tiada penalti jika bayar pada hari akhir', function () {
    $penalty = (new LatePenaltyCalculator)
        ->for(invoice(FeeType::Semester, 1100), Carbon::parse('2026-12-15 10:30'));

    expect($penalty)->toBe(0.0);
});

it('penalti tidak melebihi RM200', function () {
    $penalty = (new LatePenaltyCalculator)
        ->for(invoice(FeeType::Semester, 4800), Carbon::parse('2026-12-20'));

    expect($penalty)->toBe(200.0);
});
