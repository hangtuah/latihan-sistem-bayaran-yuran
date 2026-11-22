<?php

namespace Database\Factories;

use App\Enums\FeeType;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'number' => fake()->unique()->numerify('INV#######'),
            'type' => FeeType::Semester,
            'amount' => 1100,
            'due_date' => now()->addWeeks(2),
            'paid_at' => null,
        ];
    }

    public function overdue(): static
    {
        return $this->state(['due_date' => now()->subDays(10)]);
    }

    public function paid(): static
    {
        return $this->state(['paid_at' => now()->subDay()]);
    }
}
