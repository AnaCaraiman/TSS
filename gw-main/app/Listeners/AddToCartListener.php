<?php

namespace App\Listeners;

use App\Events\AddToCartEvent;
use App\Jobs\AddToCartJob;
class AddToCartListener
{
    public function handle(AddToCartEvent $event): void
    {
        AddToCartJob::dispatch($event->userId,$event->productId,$event->price,$event->name,$event->imageUrl)->onQueue('add_to_cart');
    }
}
