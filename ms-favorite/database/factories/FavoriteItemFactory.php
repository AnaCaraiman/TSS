<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\FavoriteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteItemFactory extends Factory
{
    protected $model = FavoriteItem::class;

    public function definition(): array
    {
        return [
            'favorite_id' => Favorite::factory(), // Automatically creates a Favorite if not provided
            'product_id' => $this->faker->unique()->numberBetween(1, 1000),
            'name' => $this->faker->words(3, true),
            'image_url' => $this->faker->imageUrl(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
