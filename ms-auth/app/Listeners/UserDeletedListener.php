<?php

namespace App\Listeners;

use App\Events\UserDeletedEvent;
use App\Jobs\DeleteCartJob;
use App\Jobs\DeleteFavoriteJob;

class UserDeletedListener
{
    public function handle(UserDeletedEvent $event): void
    {
        DeleteCartJob::dispatch($event->userId)->onQueue('delete_cart');
        DeleteFavoriteJob::dispatch($event->userId)->onQueue('delete_favorite');
    }

}
