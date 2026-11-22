<?php

use App\Models\{Invoice, User};
use Laravel\Sanctum\Sanctum;

it('pelajar melihat amaun invois sendiri', function () {
    $user = User::factory()->student()->create();
    $invoice = Invoice::factory()->for($user->student)->create(['amount' => 1100]);
    Sanctum::actingAs($user, ['invoices:read']);

    $this->getJson("/api/v1/invoices/{$invoice->id}")
        ->assertOk()
        ->assertJsonPath('data.amount', '1100.00');
});

it('pelajar tidak boleh melihat invois pelajar lain', function () {
    $user = User::factory()->student()->create();
    $other = Invoice::factory()->create();
    Sanctum::actingAs($user, ['invoices:read']);

    $this->getJson("/api/v1/invoices/{$other->id}")->assertForbidden();
});

it('Bendahari boleh melihat semua invois', function () {
    $siti = User::factory()->bursary()->create();
    $invoice = Invoice::factory()->create();
    Sanctum::actingAs($siti, ['invoices:read']);

    $this->getJson("/api/v1/invoices/{$invoice->id}")->assertOk();
});
