<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveFromFavoriteEvent
{
    use Dispatchable,SerializesModels;

    public int $userId;
    public int $productId;

    public function __construct(int $productId, int $userId) {
        $this->productId = $productId;
        $this->userId = $userId;
    }

}
