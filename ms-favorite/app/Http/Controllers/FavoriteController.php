<?php

namespace App\Http\Controllers;

use App\Services\FavoriteService;
use App\Transformers\FavoriteTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class FavoriteController
{
    public function __construct(protected FavoriteService $favoriteService){}

    public function createFavoriteCart(Request $request): JsonResponse {
        try {
            $userId = FavoriteTransformer::transform($request->data())['user_id'];

            $favorite = $this->favoriteService->createFavorite($userId);

            return response()->json([
                'message' => 'Success',
                'favorite' => $favorite
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function getFavoriteCart(Request $request): JsonResponse
    {
        try {
            $id = (int) $request->route('id');

            $favorite = $this->favoriteService->getFavoriteCart($id);

            return response()->json([
                'message' => 'Success',
                'favorite' => $favorite
            ], 201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function deleteFavoriteCart(Request $request): JsonResponse {
        try {
            $id = FavoriteTransformer::transform($request->data())['user_id'];

            $this->favoriteService->deleteFavorite($id);

            return response()->json([
                'message' => 'Success',
            ],201);
        }
        catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ],400);
        }
    }

}
