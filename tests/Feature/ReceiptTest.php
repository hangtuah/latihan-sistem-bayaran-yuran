<?php

use App\Models\{Invoice, Payment, Student, User};
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;

it('pelajar melihat resit sendiri', function () {
    $user = User::factory()->student()->create();
    $invoice = Invoice::factory()->for($user->student)->paid()->create();
    $payment = Payment::factory()->for($invoice)->for($user->student)->create();
    Sanctum::actingAs($user);

    $this->getJson("/api/v1/receipts/{$payment->id}")
        ->assertOk()
        ->assertJsonPath('invoice', $invoice->number);
});

it('pelajar tidak boleh melihat resit pelajar lain', function () {
    $payment = Payment::factory()->create();
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson("/api/v1/receipts/{$payment->id}")->assertForbidden();
});

it('resit perlukan log masuk', function () {
    $payment = Payment::factory()->create();

    $this->getJson("/api/v1/receipts/{$payment->id}")->assertUnauthorized();
});

it('laporan harian kira bayaran 7:30 pagi waktu Malaysia', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-27 10:00', 'Asia/Kuala_Lumpur'));
    Payment::factory()->create(['paid_at' => Carbon::parse('2026-11-27 07:30', 'Asia/Kuala_Lumpur')->utc()]);
    Payment::factory()->create(['paid_at' => Carbon::parse('2026-11-26 23:30', 'Asia/Kuala_Lumpur')->utc()]);
    Sanctum::actingAs(User::factory()->bursary()->create());

    $this->getJson('/api/v1/reports/payments-today')->assertOk()->assertJsonCount(1);
});

it('laporan harian guna dua query walaupun 20 bayaran', function () {
    Carbon::setTestNow(Carbon::parse('2026-11-27 10:00', 'Asia/Kuala_Lumpur'));
    Payment::factory()->count(20)->create(['paid_at' => now()->subHour()]);
    Sanctum::actingAs(User::factory()->bursary()->create());

    $this->expectsDatabaseQueryCount(2);

    $this->getJson('/api/v1/reports/payments-today')->assertOk()->assertJsonCount(20);
});
