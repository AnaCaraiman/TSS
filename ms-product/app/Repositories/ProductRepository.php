<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    public function createProduct(array $data): ?object
    {
        return Product::create([
            'name' => $data['name'] ?? '',
            'brand' => $data['brand'] ?? '',
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'category_id' => $data['category_id'] ?? '',
        ]);
    }

    public function getProducts(): array
    {
        return Product::with(['category', 'images', 'attributes'])
            ->get()
            ->toArray();
    }

    public function getProduct(int $id): object|null
    {
        return Product::with(['category', 'images', 'attributes'])
            ->where('id', $id)
            ->first();
    }


    public function removeProduct(int $id): bool
    {
        return Product::with(['category', 'images', 'attributes'])->where('id', $id)->delete() > 0;
    }

    public function getBrands(): array {
        return Product::select('brand')->distinct()->pluck('brand')->toArray();
    }

    public function lowerStock(int $productId, int $quantity): bool
    {
        Log::info('in repo');
        $product = Product::find($productId);
        Log::info($product);
        if (!$product) {
            Log::info('Product not found with id '.$productId);
            return false;
        }

        $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
        return $product->save();
    }

    public function getProductsByIds(array $ids): Collection {
        return Product::with(['category', 'images', 'attributes'])->whereIn('id', $ids)->get();
    }



}
