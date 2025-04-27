<?php

namespace App\Repositories;

use App\Models\FavoriteItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ItemsRepository
{
    public function createFavoriteItem(array $data): FavoriteItem
    {
        return FavoriteItem::create([
            'favorite_id' => $data['favorite_id'],
            'product_id' => $data['product_id'],
            'name' => $data['name'],
            'image_url' => $data['image_url'] ?? null,
            'price' => $data['price'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function deleteFavoriteItem(array $data): bool {
        $item = FavoriteItem::where('favorite_id', $data['favorite_id'])
            ->where('product_id', $data['product_id'])
            ->first();
        Log::info($item);
        if(!$item) {
            return false;
        }
        return $item->delete();
    }
}
