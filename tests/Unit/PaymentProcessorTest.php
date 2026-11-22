<?php

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Payments\PaymentGateway;
use App\Payments\PaymentProcessor;

it('caj melalui gateway yang menyokong kaedah bayaran', function () {
    $invoice = new Invoice(['number' => 'INV0000001']);

    $card = Mockery::mock(PaymentGateway::class);
    $card->shouldReceive('supports')->with(PaymentMethod::Fpx)->andReturnFalse();
    $card->shouldNotReceive('charge');

    $fpx = Mockery::mock(PaymentGateway::class);
    $fpx->shouldReceive('supports')->with(PaymentMethod::Fpx)->andReturnTrue();
    $fpx->shouldReceive('charge')->once()->with($invoice, 1100.0)->andReturn('FPX424150');

    $reference = (new PaymentProcessor([$card, $fpx]))
        ->charge($invoice, PaymentMethod::Fpx, 1100.0);

    expect($reference)->toBe('FPX424150');
});

it('gagal jika tiada gateway menyokong kaedah bayaran', function () {
    $fpx = Mockery::mock(PaymentGateway::class);
    $fpx->shouldReceive('supports')->andReturnFalse();

    (new PaymentProcessor([$fpx]))
        ->charge(new Invoice, PaymentMethod::Counter, 50.0);
})->throws(LogicException::class, 'Kaedah bayaran tidak disokong: counter');
