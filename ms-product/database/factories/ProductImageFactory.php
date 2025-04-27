<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'image_url' => $this->faker->imageUrl(600, 600, 'technics', true),
            'is_primary' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

