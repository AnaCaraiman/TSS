<?php

namespace App\Listeners;

use App\Events\ClearCartEvent;
use App\Jobs\ClearCartJob;
use Illuminate\Support\Facades\Log;

class ClearCartListener
{
    public function handle(ClearCartEvent $event): void
    {
        Log::info('listener triggered');
        ClearCartJob::dispatch($event->userId)->onQueue('clear_cart');
    }

}
