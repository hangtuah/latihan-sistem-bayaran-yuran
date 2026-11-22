<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

// SDK ringkas untuk API bank. Kod asal memanggil class ini terus dari controller.
class FpxClient
{
    public function __construct(private ?string $key) {}

    public function pay(string $invoiceNumber, float $amount): string
    {
        return Http::withToken((string) $this->key)
            ->post(config('services.fpx.url').'/charge', ['invoice' => $invoiceNumber, 'amount' => $amount])
            ->throw()
            ->json('reference');
    }
}
