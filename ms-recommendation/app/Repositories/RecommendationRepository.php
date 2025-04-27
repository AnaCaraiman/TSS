<?php

namespace App\Repositories;

use App\Models\Action;

class RecommendationRepository
{

    public function createAction(int $userId, int $productId, int $actionId): Action
    {
        return Action::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'action_id' => $actionId
        ]);
    }

    public function getActions(): array
    {
        return Action::all()->toArray();
    }
}
