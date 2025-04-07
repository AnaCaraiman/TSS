<?php

namespace App\Http\Controllers;

use App\Services\CartItemService;
use App\Services\CartService;
use App\Transformers\CartItemTransformer;
use App\Transformers\CartTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService, protected CartItemService $cartItemService) {}

    public function createCart(Request $request): JsonResponse {
        try {
            $data = $request->all();
            $cart = CartTransformer::transform($data);
            $this->cartService->createCart($cart);

            return response()->json([
                'message' => 'Cart created successfully',
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }


    public function getCart(Request $request): JsonResponse {
        try{
            $userId = (int) $request->route('id');
            $cart = $this->cartService->getCart($userId);
            return response()->json([
                'message' => 'Cart retrieved successfully for user with id ' . $userId,
                'cart' => $cart
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function deleteCart(Request $request): JsonResponse {
        try{
            $userId = (int) $request->route('id');
            $this->cartService->deleteCart($userId);
            return response()->json([
                'message' => 'Cart deleted successfully',
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }


}
