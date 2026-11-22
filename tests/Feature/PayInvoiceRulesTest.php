<?php

use App\Models\{Invoice, Payment, User};
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Queue::fake();
    $this->aisyah = User::factory()->student()->create();
    Sanctum::actingAs($this->aisyah, ['payments:create']);
});

it('tidak boleh bayar invois pelajar lain', function () {
    $badrul = User::factory()->student()->create();
    $invoice = Invoice::factory()->for($badrul->student)->create();

    $this->postJson('/api/v1/payments', ['invoice_id' => $invoice->id, 'method' => 'fpx'])
        ->assertForbidden();

    $this->assertDatabaseCount('payments', 0);
});

it('menolak kaedah bayaran yang tidak wujud', function () {
    $invoice = Invoice::factory()->for($this->aisyah->student)->create();

    $this->postJson('/api/v1/payments', ['invoice_id' => $invoice->id, 'method' => 'cash'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['method']);
});

it('tidak boleh bayar invois yang sudah dibayar', function () {
    $invoice = Invoice::factory()->for($this->aisyah->student)->paid()->create();

    $this->postJson('/api/v1/payments', ['invoice_id' => $invoice->id, 'method' => 'fpx'])
        ->assertStatus(409);

    $this->assertDatabaseCount('payments', 0);
});

it('caj penalti jika invois lewat', function () {
    $invoice = Invoice::factory()->for($this->aisyah->student)->overdue()->create(['amount' => 920]);

    $this->postJson('/api/v1/payments', ['invoice_id' => $invoice->id, 'method' => 'fpx'])
        ->assertCreated();

    expect(Payment::sole()->amount)->toBe('966.00');   // 920 + 5%
});

it('token tanpa ability payments:create ditolak', function () {
    Sanctum::actingAs($this->aisyah, ['invoices:read']);
    $invoice = Invoice::factory()->for($this->aisyah->student)->create();

    $this->postJson('/api/v1/payments', ['invoice_id' => $invoice->id, 'method' => 'fpx'])
        ->assertForbidden();
});
