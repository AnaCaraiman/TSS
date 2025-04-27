<?php

namespace App\Http\Controllers;

use App\Events\AddToFavoriteEvent;
use App\Events\RemoveFromFavoriteEvent;
use App\Transformers\CartResponseTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class FavoriteController
{
    private string $favoriteServiceUrl;
    public function __construct(protected AuthController $authController,protected CartResponseTransformer $cartResponseTransformer){
        $this->favoriteServiceUrl = config('services.ms_favorite.url');
    }
    public function getFavorites(Request $request): JsonResponse {
        try{
            $id = $this->authController->getUserId($request);
            $response = Http::get($this->favoriteServiceUrl . '/api/favorite/'. $id);

            $data = CartResponseTransformer::transform($response,'original');
            return response()->json($data);
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(),401);
        }
    }

    public function addToFavorite(Request $request): JsonResponse {
        try{
        $data = $request->all();
        $id = $this->authController->getUserId($request);

        Event::dispatch(new AddToFavoriteEvent($data,$id));

        return response()->json([
            'message' => 'Added to favorite',
        ],201);
        }
        catch (Exception $e) {
            return response()->json($e->getMessage(),401);
        }
    }

    public function removeFromFavorite(Request $request): JsonResponse {
        try{
            $productId = (int) $request->route('id');
            $userId = $this->authController->getUserId($request);

            Event::dispatch(new RemoveFromFavoriteEvent($productId,$userId));

            return response()->json([
                'message' => 'Removed from favorite',
            ],201);

        }
        catch (Exception $e) {
            return response()->json($e->getMessage(),401);
        }
    }

}
