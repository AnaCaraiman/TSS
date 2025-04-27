<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'attribute_name' => $this->faker->randomElement(['Material', 'Dimensiune', 'Greutate', 'Culoare']),
            'attribute_value' => $this->faker->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

