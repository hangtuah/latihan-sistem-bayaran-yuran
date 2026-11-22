<?php

namespace Database\Factories;

use App\Models\Programme;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'programme_id' => Programme::factory(),
            'matric' => fake()->unique()->numerify('A23CS####'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    /** Setiap pelajar dapat akaun log masuk, supaya $student->user wujud. */
    public function configure(): static
    {
        return $this->afterCreating(function (Student $student) {
            User::factory()->create([
                'name' => $student->name,
                'email' => $student->email,
                'student_id' => $student->id,
            ]);
        });
    }
}
