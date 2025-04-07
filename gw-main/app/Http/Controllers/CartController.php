<?php

namespace App\Http\Controllers;

use App\Events\AddToCartEvent;
use App\Events\ClearCartEvent;
use App\Transformers\CartResponseTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartController
{
    private string $cartServiceUrl;
    public function __construct(protected AuthController $authController){
        $this->cartServiceUrl = config('services.ms_cart.url');
    }

    public function getCartTotal(Request $request): float {
        $cartResponse = $this->getCart($request);
        $cartData = $cartResponse->getData();

        $cartItems = collect($cartData->cart->cart_items);

        return $cartItems->sum(fn($item) => $item->price * $item->quantity);
    }

    public function getCartItems(Request $request): array {
        $cartResponse = $this->getCart($request);
        $cartData = $cartResponse->getData();
        $cartItems = collect($cartData->cart->cart_items);

        return $cartItems->map(function ($item) {
            return [
                'id' => $item->product_id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image_url' => $item->image_url ?? null,
            ];
        })->toArray();
    }

    public function getCart(Request $request): JsonResponse{
        try {
            $userId = $this->authController->getUserId($request);

            $response = Http::get($this->cartServiceUrl . '/api/ms-cart/' . $userId);
            $data = CartResponseTransformer::transform($response,'original');

            return response()->json($data);
        }
        catch(Exception $e){
            return response()->json($e->getMessage(),401);
        }
    }

    public function addToCart(Request $request): JsonResponse{
        try {
            $data = $request->all();
            $userId = $this->authController->getUserId($request);
            Event::dispatch(new AddToCartEvent($data,$userId));

            return response()->json([
                'message' => 'Added to cart successfully for user with id '. $userId
            ],201);
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(),400);
        }
    }

    /**
     */
    public function updateCart(Request $request): JsonResponse{
        try {
            $data = $request->all();
            $data['user_id'] = $this->authController->getUserId($request);

            $response = Http::put($this->cartServiceUrl . '/api/ms-cart/', $data);
            $data = CartResponseTransformer::transform($response, 'cart');

            return response()->json($data);
        }
        catch(Exception $e){
            return response()->json($e->getMessage(),400);
        }

    }

    public function clearCart(Request $request): JsonResponse{
        try{
            $userId = $this->authController->getUserId($request);
            Event::dispatch(new ClearCartEvent($userId));

            return response()->json([
                'message' => 'Cleared cart successfully for used with id '. $userId
            ],201);
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(),400);
        }
    }

}

