<?php

namespace App\Services;

use App\Repositories\CartRepository;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CartService
{
    public function __construct(protected CartRepository $cartRepository) {}

    /**
     * @throws Exception
     */
    public function createCart(array $data): void {
        if(!$this->cartRepository->createCart($data)) {
            throw new Exception("Cart not created.");
        }

        $cart = $this->cartRepository->getCart($data['user_id']);
        Log::info($cart);
        Cache::put('cart_user_' . $cart->user_id, $cart,1800);
    }

    /**
     * @throws Exception
     */
    public function getCart(int $userId): object|null {
        $cached = Cache::get('cart_user_' . $userId);
        if($cached) {
            return $cached;
        }
        return $this->cartRepository->getCart($userId);

    }

    /**
     * @throws Exception
     */
    public function deleteCart(int $userId): void {
        Cache::forget('cart_user_' . $userId);
        if(!$this->cartRepository->deleteCart($userId)) {
            throw new Exception("Cart not deleted.");
        }
    }

    public function clearCart(int $userId): void {
        Cache::forget('cart_user_' . $userId);
        $cartId = $this->cartRepository->getCartId($userId);
        $cart = $this->cartRepository->clearCart($cartId);
        if(!$cart) {
            throw new Exception("Cart not cleared.");
        }
        Cache::put('cart_user_' . $userId,$cart,1800);
    }








}
