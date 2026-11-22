<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory()->paid(),
            'student_id' => fn (array $attributes) => Invoice::find($attributes['invoice_id'])->student_id,
            'method' => PaymentMethod::Fpx,
            'amount' => 1100,
            'reference' => fake()->unique()->numerify('FPX######'),
            'paid_at' => now(),
        ];
    }
}
