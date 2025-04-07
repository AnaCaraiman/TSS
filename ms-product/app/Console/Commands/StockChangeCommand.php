<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class StockChangeCommand extends Command
{
    protected $signature = 'rabbitmq:consume-stock-change';
    protected $description = 'Consumes messages from the stock_change queue and decrements product stock';

    public function __construct(protected ProductService $productService)
    {
        parent::__construct();
        Log::info("Stock change consumer command initialized.");
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', '127.0.0.1'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest')
            );

            $channel = $connection->channel();
            $channel->queue_declare('stock_change', false, true, false, false);
            Log::info("Listening for messages on 'stock_change' queue...");

            $callback = function (AMQPMessage $msg) use ($channel) {
                try {
                    $messageData = json_decode($msg->getBody(), true);

                    if (!isset($messageData['products']) || !is_array($messageData['products'])) {
                        Log::error('Invalid message format: "products" key missing or not an array.');
                        $channel->basic_ack($msg->getDeliveryTag());
                        return;
                    }

                    foreach ($messageData['products'] as $product) {
                        $productId = $product['product_id'] ?? null;
                        $quantity  = $product['quantity'] ?? 0;

                        if ($productId === null) {
                            Log::error('Skipped product: missing product_id.');
                            continue;
                        }

                        Log::info("Updating stock: product_id = $productId, quantity = $quantity");
                        $this->productService->decrementStock($productId, $quantity);
                    }

                    $channel->basic_ack($msg->getDeliveryTag());

                } catch (Throwable $e) {
                    Log::error("Error while processing stock change message: " . $e->getMessage());
                    $channel->basic_nack($msg->getDeliveryTag(), false, true);
                }
            };

            $channel->basic_consume('stock_change', '', false, false, false, false, $callback);

            while ($channel->is_consuming()) {
                $channel->wait();
            }

            $channel->close();
            $connection->close();

        } catch (Throwable $e) {
            Log::error("Fatal error in StockChangeCommand: " . $e->getMessage());
        }
    }
}
