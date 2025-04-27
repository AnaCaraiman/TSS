<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Jobs\CreateCartJob;
use App\Jobs\CreateFavoriteJob;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\Log;

class UserRegisteredListener
{
    public function handle(UserRegisteredEvent $event): void
    {
        Log::info("📥 UserRegisteredListener triggered for user ID: {$event->user->id}");
        CreateCartJob::dispatch($event->user->id)->onQueue('create_cart');
//        SendMailJob::dispatch($event->user)->onQueue('mail');
        CreateFavoriteJob::dispatch($event->user->id)->onQueue('create_favorite');
    }
}
