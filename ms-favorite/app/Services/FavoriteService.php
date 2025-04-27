<?php

namespace App\Services;

use App\Models\Favorite;
use App\Repositories\FavoriteRepository;
use Exception;

class FavoriteService
{
    public function __construct(protected FavoriteRepository $favoriteRepository){}

    public function createFavorite(int $userId): Favorite {
        return $this->favoriteRepository->createFavoriteCart($userId);
    }

    /**
     * @throws Exception
     */
    public function getFavoriteCart(int $userId): ?Favorite {
        $favorite = $this->favoriteRepository->getFavoriteCart($userId);
        if(!$favorite) {
            throw new Exception('Favorite not found');
        }
        return $favorite;

    }

    /**
     * @throws Exception
     */
    public function deleteFavorite(int $userId): void {
        if(!$this->favoriteRepository->deleteFavoriteCart($userId)){
            throw new Exception('Failed to delete favorite');
        }
    }

}
