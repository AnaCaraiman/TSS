<?php

namespace App\Http\Controllers;

use App\Services\ItemService;
use App\Transformers\FavoriteItemTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteItemController
{
    public function __construct(protected ItemService $itemService){}

    public function addFavoriteItem(Request $request): JsonResponse {
        try{
            $favoriteId = $this->itemService->getFavoriteIdByUserId($data['user_id'] ?? null);
            if (!$favoriteId) {
                throw new Exception('Favorite cart not found for user');
            }

            $data['favorite_id'] = $favoriteId;


            $data = FavoriteItemTransformer::transform($request->all());
            $item = $this->itemService->createItem($data);

        return response()->json([
            'message' => 'Item added to favorite list',
            'item' => $item
        ],201);
        }
        catch (Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ],400);
        }
    }

    public function removeFavoriteItem(Request $request): JsonResponse {
        try {
            $this->itemService->deleteItem($request->all());

            return response()->json([
                'message' => 'Item removed from favorite list'
            ],201);
        }
        catch (Exception $exception){
            return response()->json([
                'message' => $exception->getMessage()
            ],400);
        }
    }

}
