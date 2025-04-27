<?php

namespace App\Services;

use App\Models\Action;
use App\Repositories\RecommendationRepository;

class RecommendationService
{
    public function __construct(protected RecommendationRepository $recommendationRepository){}

    public function addAction(int $userId, int $productId, int $actionId): Action
    {
        return $this->recommendationRepository->createAction($userId, $productId, $actionId);
    }

    public function getActions(): array
    {
        return $this->recommendationRepository->getActions();
    }
}
