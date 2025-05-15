<?php

namespace App\Services;

use App\Models\FavoriteItem;
use App\Repositories\FavoriteRepository;
use App\Repositories\ItemsRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class ItemService
{
    public function __construct(protected ItemsRepository $itemsRepository,protected FavoriteRepository $favoriteRepository)
    {
    }

    public function getFavoriteIdByUserId(?int $userId): ?int {
        if (!$userId) return null;
        return $this->favoriteRepository->getFavoriteCartId($userId);
    }
    public function createItem(array $data): FavoriteItem {
        $data['favorite_id'] = $this->favoriteRepository->getFavoriteCartId($data['user_id']);

        return $this->itemsRepository->createFavoriteItem($data);
    }

    /**
     * @throws Exception
     */
    public function deleteItem(array $data): void {
        $data['favorite_id'] = $this->favoriteRepository->getFavoriteCartId($data['user_id']);
        Log::info($data);
        if(!$this->itemsRepository->deleteFavoriteItem($data)) {
            throw new Exception('Failed to delete favorite item.');
        }
    }

}
