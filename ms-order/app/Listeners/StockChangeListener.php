<?php

namespace App\Listeners;

use App\Events\StockChangeEvent;
use App\Jobs\StockChangeJob;
use Illuminate\Support\Facades\Log;

class StockChangeListener
{
    public function handle(StockChangeEvent $event): void {
        Log::info('listener');
        StockChangeJob::dispatch($event->products)->onQueue('stock_change');
    }

}
