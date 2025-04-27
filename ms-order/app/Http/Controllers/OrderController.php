<?php

namespace App\Http\Controllers;

use App\Events\StockChangeEvent;
use App\Services\OrderService;
use App\Transformers\OrderTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class OrderController
{
    public function __construct(protected OrderService $orderService){}

    public function makeOrder(Request $request): JsonResponse {
        try{
            $data = $request->all();
            $orderData = OrderTransformer::transform($data);
            $order = $this->orderService->addOrder($orderData);
            Event::dispatch(new StockChangeEvent($order));
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ],201);
        }
        catch(Exception $e){
            return response()->json([
            'error' => $e->getMessage()
            ],400);
        }
    }

    public function getOrders(Request $request): JsonResponse
    {
        $userId = $request->query('user_id');
        $page = $request->query('page', 1);

        Log::info("Fetching orders for user_id: $userId, page: $page");

        $orders = $this->orderService->getOrders((int) $userId, (int) $page);

        return response()->json([
            'message' => 'User orders retrieved successfully',
            'orders' => $orders->items(),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'has_more_pages' => $orders->hasMorePages(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ]);
    }


    public function getOrder(Request $request): JsonResponse {
        try {
            $orderId = $request->route('orderId');
            $order = $this->orderService->getOrder($orderId);
            return response()->json([
                'message' => 'Order retrieved successfully',
                'order' => $order
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function cancelOrder(Request $request): JsonResponse {
        try {
            $orderId = $request->route('orderId');
            $this->orderService->cancelOrder($orderId);
            return response()->json([
                'message' => 'Order cancelled successfully'
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }

    }

}
