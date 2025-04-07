<?php

namespace App\Repositories;

use App\Models\Cart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    public function __construct(){}

    public function createCart(array $data): bool
    {
        return DB::table('carts')->insert([
            'user_id' => $data['user_id'],
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
    }
    public function getCart(int $userId): object|null {
        return Cart::with(['cartItems'])
            ->where('user_id', $userId)
            ->first();
    }

    public function getCartId(int $userId): int|null{
        return DB::table('carts')
            ->where('user_id', $userId)
            ->first()->id;
    }

    public function deleteCart(int $userId):bool
    {
        return Cart::with(['cartItems'])
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    public function clearCart(int $cartId): bool {
        return DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->delete() > 0;
    }

}
