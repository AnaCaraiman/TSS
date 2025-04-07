<?php

namespace App\Events;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
class StockChangeEvent
{
    use Dispatchable,SerializesModels;

    public Collection $products;

    public function __construct(Order $order) {
        $this->products = $order->products;
    }
}
