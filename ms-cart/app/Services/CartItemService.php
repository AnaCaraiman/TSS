<?php

namespace App\Services;

use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CartItemService
{
    public function __construct(
        protected CartItemRepository $cartItemRepository, protected CartRepository $cartRepository) {}
    /**
     * @throws Exception
     */
    public function updateCart(array $data): ?object {
        $operation = $data['operation'];
        if($operation === '+') {
            return $this->addQuantityToCartItem($data);
        }
        if($operation === "-") {
            return $this->removeQuantityFromCartItem($data);
        }
        else{
            return $this->removeCartItem($data);
        }
    }

    /**
     * @throws Exception
     */
    public function addCartItem(array $data): ?object {
        $data['cart_id'] = $this->cartRepository->getCartId($data['user_id']);
        if(!$this->cartItemRepository->addCartItem($data)) {
            throw new Exception('Failed to add cart item.');
        }
        return $this->updateCacheAndGetCart($data['user_id']);
    }

    public function removeCartItem(array $data): ?object
    {
        $data['cart_id'] = $this->cartRepository->getCartId($data['user_id']);
        if (!$this->cartItemRepository->removeCartItem($data)) {
            throw new Exception('Failed to remove cart item.');
        }
        return $this->updateCacheAndGetCart($data['user_id']);
    }


    /**
     * @throws Exception
     */
    public function addQuantityToCartItem(array $data): ?object
    {
        $data['cart_id'] = $this->cartRepository->getCartId($data['user_id']);
        if (!$this->cartItemRepository->addQuantityToCartItem($data)) {
            throw new Exception('Failed to add cart item.');
        }
        return $this->updateCacheAndGetCart($data['user_id']);
    }

    /**
     * @throws Exception
     */
    public function removeQuantityFromCartItem(array $data): ?object
    {
        $data['cart_id'] = $this->cartRepository->getCartId($data['user_id']);
        if (!$this->cartItemRepository->removeQuantityFromCartItem($data)) {
            throw new Exception('Failed to remove cart item.');
        }
        return $this->updateCacheAndGetCart($data['user_id']);
    }



    protected function updateCacheAndGetCart(int $userId): object
    {
        $cart = $this->cartRepository->getCart($userId);

        $cacheKey = 'cart_user_' . $userId;
        Cache::forget($cacheKey);
        Cache::put($cacheKey, $cart, 1800);

        return $cart;
    }


}
