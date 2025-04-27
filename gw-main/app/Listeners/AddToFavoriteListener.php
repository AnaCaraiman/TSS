<?php

namespace App\Listeners;

use App\Events\AddToFavoriteEvent;
use App\Jobs\AddToFavoriteJob;

class AddToFavoriteListener
{
    public function handle(AddToFavoriteEvent $event): void {
        AddToFavoriteJob::dispatch($event->userId,$event->productId,$event->price,$event->name,$event->imageUrl)->onQueue('add_to_favorite');
    }

}
