<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

// SDK ringkas untuk API bank. Kod asal memanggil class ini terus dari controller.
class EWalletClient
{
    public function __construct(private ?string $key) {}

    public function debit(float $amount): string
    {
        return Http::withToken((string) $this->key)
            ->post(config('services.ewallet.url').'/charge', ['amount' => $amount])
            ->throw()
            ->json('reference');
    }
}
