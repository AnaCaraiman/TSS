<?php

namespace App\Http\Controllers;

use App\Services\CartItemService;
use App\Transformers\CartItemTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartItemController extends Controller
{
    public function __construct(protected CartItemService $cartItemService){}

    public function updateCart(Request $request): JsonResponse
    {
        try {
            Log::info('updating cart');
            $requestData = $request->all();
            $data = CartItemTransformer::transform($requestData);
            $cart = $this->cartItemService->updateCart($data);
            return response()->json([
                'message' => 'Item removed from cart',
                'cart' => $cart
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
