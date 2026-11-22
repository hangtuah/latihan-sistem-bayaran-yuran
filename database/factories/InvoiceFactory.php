<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'number' => fake()->unique()->numerify('INV#######'),
            'type' => 'semester',
            'amount' => 1100,
            'due_date' => now()->addWeeks(2),
            'paid_at' => null,
        ];
    }
}
