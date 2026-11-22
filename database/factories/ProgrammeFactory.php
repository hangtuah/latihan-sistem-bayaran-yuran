<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('SEC??'),
            'name' => 'Sarjana Muda Sains Komputer',
        ];
    }
}
