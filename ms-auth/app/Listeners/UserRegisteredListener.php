<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Jobs\CreateCartJob;

class UserRegisteredListener
{
    public function handle(UserRegisteredEvent $event): void
    {
        CreateCartJob::dispatch($event->userId)->onQueue('create_cart');
    }
}
