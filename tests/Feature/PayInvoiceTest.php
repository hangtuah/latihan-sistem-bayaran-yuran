<?php

use App\Jobs\SyncPaymentToFinance;
use App\Listeners\SendPaymentReceipt;
use App\Models\{Invoice, User};
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

it('pelajar boleh bayar invois sendiri', function () {
    Queue::fake();

    $user = User::factory()->student()->create();
    $invoice = Invoice::factory()->for($user->student)->create(['amount' => 1100]);

    Sanctum::actingAs($user, ['payments:create']);

    $this->postJson('/api/v1/payments', [
        'invoice_id' => $invoice->id,
        'method' => 'fpx',
    ])->assertCreated();

    $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'method' => 'fpx']);
    expect($invoice->fresh()->paid_at)->not->toBeNull();

    Queue::assertPushed(SyncPaymentToFinance::class);
    Queue::assertPushed(CallQueuedListener::class,
        fn ($job) => $job->class === SendPaymentReceipt::class);
});
