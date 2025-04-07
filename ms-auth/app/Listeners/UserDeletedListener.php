<?php

namespace App\Listeners;

use App\Events\UserDeletedEvent;
use App\Events\UserRegisteredEvent;
use App\Jobs\CreateCartJob;
use App\Jobs\DeleteCartJob;

class UserDeletedListener
{
    public function handle(UserDeletedEvent $event): void
    {
        DeleteCartJob::dispatch($event->userId)->onQueue('delete_cart');
    }

}
