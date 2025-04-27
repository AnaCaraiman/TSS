<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecommendationController extends Controller
{
    public function __construct(protected RecommendationService $recommendationService) {}
    public function getMostPopularProducts(Request $request): JsonResponse {
        $userId = (int) $request->route('id');

        $response = Http::post('http://ms-ai-recommendation:8000/api/recommend', [
            'user_id' => $userId
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'AI service unavailable'
            ], 500);
        }
        return response()->json([
            'user_id' => $userId,
            'recommended_products' => $response->body()
        ]);
    }

    public function getPersonalisedProducts(Request $request): JsonResponse {
        $userId = $request->route('id');

        $response = Http::post('http://ms-ai-recommendation:8000/api/recommend/personalised', [
            'user_id' => $userId
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'AI service unavailable'
            ], 500);
        }
        return response()->json([
            'user_id' => $userId,
            'recommended_products' => $response->body()
        ]);
    }

    public function addUserAction(Request $request): JsonResponse
    {
        try {
            $userId = (int)$request->input('user_id');
            $productId = (int)$request->input('product_id');
            $actionId = (int)$request->input('action_id');

            $action = $this->recommendationService->addAction($userId, $productId, $actionId);

            return response()->json([
                'message' => 'Action added',
                'action' => $action
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ],400);
        }
    }

    public function getActions(): JsonResponse {
        return response()->json([
            'message' => 'Actions retrieved successfully',
            'actions' => $this->recommendationService->getActions()
        ],201);
    }
}
