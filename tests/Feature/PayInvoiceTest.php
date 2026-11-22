<?php

use App\Mail\PaymentReceipt;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

// Ujian ini mesti kekal hijau sepanjang refactor Modul 1.
// Ia menguji tingkah laku (apa yang pelajar nampak), bukan struktur kod.

beforeEach(function () {
    Http::fake(['*' => Http::response(['reference' => 'FPX424150'])]);
    Mail::fake();
});

it('merekod bayaran dan tanda invois sudah dibayar', function () {
    $student = Student::factory()->create();
    $invoice = Invoice::factory()->for($student)->create([
        'amount' => 1250, 'due_date' => now()->addWeek(),
    ]);

    $this->actingAs($student->user)->post('/payments', [
        'invoice_id' => $invoice->id,
        'method'     => 'fpx',
    ])->assertRedirect();

    expect(Payment::sole()->amount)->toEqual(1250.0)   // belum lewat: tiada penalti
        ->and($invoice->fresh()->paid_at)->not->toBeNull();
});

it('caj penalti 5% untuk yuran semester yang lewat', function () {
    $student = Student::factory()->create();
    $invoice = Invoice::factory()->for($student)->create([
        'type' => 'semester', 'amount' => 1100, 'due_date' => now()->subDays(10),
    ]);

    $this->actingAs($student->user)->post('/payments', [
        'invoice_id' => $invoice->id,
        'method'     => 'card',
    ])->assertRedirect();

    expect((float) Payment::sole()->amount)->toBe(1155.0);
});

it('hantar resit melalui emel', function () {
    $student = Student::factory()->create();
    $invoice = Invoice::factory()->for($student)->create();

    $this->actingAs($student->user)->post('/payments', [
        'invoice_id' => $invoice->id,
        'method'     => 'ewallet',
    ]);

    Mail::assertSent(PaymentReceipt::class);
});

it('tidak merekod bayaran untuk invois yang sudah dibayar', function () {
    $student = Student::factory()->create();
    $invoice = Invoice::factory()->for($student)->create(['paid_at' => now()]);

    $this->actingAs($student->user)->post('/payments', [
        'invoice_id' => $invoice->id,
        'method'     => 'fpx',
    ])->assertSessionHasErrors('invoice_id');

    expect(Payment::count())->toBe(0);
});

it('menolak kaedah bayaran yang tidak wujud', function () {
    $student = Student::factory()->create();
    $invoice = Invoice::factory()->for($student)->create();

    $this->actingAs($student->user)->post('/payments', [
        'invoice_id' => $invoice->id,
        'method'     => 'cash',
    ])->assertSessionHasErrors('method');

    expect(Payment::count())->toBe(0);
});
