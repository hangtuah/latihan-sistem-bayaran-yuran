<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

// SDK ringkas untuk API bank. Kod asal memanggil class ini terus dari controller.
class CardClient
{
    public function __construct(private ?string $key) {}

    public function charge(float $amount): string
    {
        return Http::withToken((string) $this->key)
            ->post(config('services.card.url').'/charge', ['amount' => $amount])
            ->throw()
            ->json('reference');
    }
}
