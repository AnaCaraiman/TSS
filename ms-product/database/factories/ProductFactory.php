<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $brandCategoryMap = [
        'Daco' => 'Accesorii Școlare',
        'Leitz' => 'Papetărie',
        'Rowenta' => 'Fitness & Wellness',
        'Philips' => 'Fitness & Wellness',
        'Star-Light' => 'Fitness & Wellness',
        'Tefal' => 'Fitness & Wellness',
        'Heinner' => 'Fitness & Wellness',
        'Oral-B' => 'Fitness & Wellness',
        'Logitech' => 'Papetărie',
        'Redragon' => 'Papetărie',
    ];

    public function definition(): array
    {
        $brand = $this->faker->randomElement(array_keys($this->brandCategoryMap));
        $categoryName = $this->brandCategoryMap[$brand];

        $category = Category::firstOrCreate(['name' => $categoryName], [
            'description' => $this->faker->sentence(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $price = $this->faker->randomFloat(2, 10, 1000);
        if ($this->faker->boolean(80)) {
            $price = $this->faker->randomFloat(2, 10, 250);
        }

        return [
            'name' => ucfirst($this->faker->unique()->words(3, true)),
            'brand' => $brand,
            'description' => $this->faker->sentence(12),
            'price' => round($price, 2),
            'stock_quantity' => $this->faker->biasedNumberBetween(0, 200, 'sqrt'),
            'category_id' => $category->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
