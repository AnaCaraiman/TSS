<?php

namespace App\Repositories;

use App\Models\Favorite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FavoriteRepository
{
    public function createFavoriteCart(int $userId): Favorite
    {
        return Favorite::create([
            'user_id' => $userId,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now(),
        ]);
    }

    public function getFavoriteCart(int $userId): ?Favorite
    {
        return Favorite::with('items')->where('user_id', $userId)->first();
    }

    public function getFavoriteCartId(int $userId): ?int {
        return Favorite::where('user_id', $userId)->first()->id;
    }

    public function deleteFavoriteCart(int $userId): bool
    {
        $favorite = $this->getFavoriteCart($userId);

        if ($favorite) {
            return $favorite->delete();
        }

        return false;
    }
}
