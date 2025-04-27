<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create();

        Product::factory(150)->create()->each(function ($product) {
            ProductImage::factory(rand(1, 3))->create([
                'product_id' => $product->id,
                'is_primary' => false,
            ])->first()->update(['is_primary' => true]);

            ProductAttribute::factory(rand(2, 4))->create([
                'product_id' => $product->id,
            ]);
        });
    }
}

