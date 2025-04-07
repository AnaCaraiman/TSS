<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CartItemRepository
{
    public function __construct(){}

    public function addQuantityToCartItem(array $data): bool {
        return DB::table('cart_items')
            ->where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->increment('quantity');
    }

    public function addCartItem(array $data): bool {
        $exists = DB::table('cart_items')
            ->where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->exists();
        if ($exists) {
            return $this->addQuantityToCartItem($data);
        }
        return DB::table('cart_items')->insert([
            "cart_id" => $data['cart_id'],
            "product_id" => $data['product_id'],
            "quantity" => 1,
            "name" => $data['name'],
            "price" => $data['price'],
            "image_url" => $data['image_url'],
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);
    }

    public function removeQuantityFromCartItem(array $data): bool
    {
        return DB::table('cart_items')
            ->where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->decrement('quantity');
    }

    public function removeCartItem(array $data): bool {
        return DB::table('cart_items')
            ->where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->delete() > 0;
    }





}
