<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecommendationController
{
    private string $recommendationServiceUrl;
    public function __construct(protected AuthController $authController,protected ProductController $productController) {
        $this->recommendationServiceUrl = config('services.ms_recommendation.url');
    }

    public function getRecommendedProducts(Request $request): JsonResponse {
        try {
            $productId = $request->route('id');

            $response = Http::get($this->recommendationServiceUrl . '/api/recommendation/'. $productId );

            $data = json_decode($response->body(), true);
            $recommendedIds = json_decode($data['recommended_products'], true);

            return $this->productController->getProductsByIds($recommendedIds);
        }
        catch(Exception $e){
            return response()->json(json_decode($e->getMessage(), true));
        }
    }

    public function addAction (Request $request): JsonResponse {
        try {
            $userId = $this->authController->getUserId($request);
            $productId = $request->input('product_id');
            $actionId = $request->input('action_id');

            $response = Http::post($this->recommendationServiceUrl . '/api/recommendation', [
                'user_id' => $userId,
                'product_id' => $productId,
                'action_id' => $actionId
            ]);

            return response()->json(json_decode($response->getBody()->getContents(), true));
        }
        catch(Exception $e){
            return response()->json(json_decode($e->getMessage(), true));
        }
    }


}
