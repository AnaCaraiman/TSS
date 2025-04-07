<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AddToCartEvent
{
    use Dispatchable,SerializesModels;

    public int $userId;
    public int $productId;
    public float $price;
    public string $name;
    public string $imageUrl;

    public function __construct(array $data, int $userId)
    {

        $this->userId = $userId;
        $this->productId = $data['productId'];
        $this->price = $data['price'];
        $this->name = $data['name'];
        $this->imageUrl = $data['image_url'] ?? '';
        Log::info('reached event dispatch',
            [$this->userId,
            $this->productId,
            $this->price,
            $this->name,
            $this->imageUrl]);
    }


}
