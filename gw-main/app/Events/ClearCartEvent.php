<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClearCartEvent
{
    use Dispatchable,SerializesModels;

    public int $userId;

    public function __construct(int $userId) {
        $this->userId = $userId;
    }

}
