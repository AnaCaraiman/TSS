<?php

namespace App\Listeners;

use App\Events\RemoveFromFavoriteEvent;
use App\Jobs\RemoveFromFavoriteJob;

class RemoveFromFavoriteListener
{
    public function handle(RemoveFromFavoriteEvent $event):void {
        RemoveFromFavoriteJob::dispatch($event->userId,$event->productId)->onQueue('remove_from_favorite');
    }

}
