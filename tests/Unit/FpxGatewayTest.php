<?php

use App\Models\Invoice;
use App\Payments\FpxGateway;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config(['services.fpx.url' => 'https://fpx.test/charge', 'services.fpx.key' => 'kunci-ujian']);
});

it('hantar nombor invois dan amaun ke FPX', function () {
    Http::fake(['fpx.test/*' => Http::response(['reference' => 'FPX424150'])]);

    $reference = (new FpxGateway)->charge(new Invoice(['number' => 'INV0040006']), 1155.0);

    expect($reference)->toBe('FPX424150');
    Http::assertSent(fn (Request $request) =>
        $request->hasHeader('Authorization', 'Bearer kunci-ujian')
        && $request['invoice'] === 'INV0040006'
        && $request['amount'] === 1155.0);
});

it('gagal jika FPX pulangkan ralat', function () {
    Http::fake(['fpx.test/*' => Http::response(['message' => 'Bank tidak tersedia'], 503)]);

    (new FpxGateway)->charge(new Invoice(['number' => 'INV0040006']), 1155.0);
})->throws(RequestException::class);
