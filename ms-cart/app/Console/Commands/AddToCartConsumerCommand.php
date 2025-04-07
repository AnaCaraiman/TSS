<?php

namespace App\Console\Commands;

use App\Services\CartItemService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AddToCartConsumerCommand extends Command
{
    protected $signature = 'rabbitmq:consume-add-to-cart';
    protected $description = 'Consumes messages from the add_to_cart queue and adds a product to the cart.';

    public function __construct(protected CartItemService $cartItemService)
    {
        parent::__construct();
        Log::info("Add to cart consumer command constructed successfully.");
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $channel = $connection->channel();
        $channel->queue_declare('add_to_cart', false, true, false, false);
        Log::info("Listening for messages on 'add_to_cart' queue...");

        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            if (isset($messageData['data']['command'])) {
                $command = $messageData['data']['command'];
                Log::info("Received message with command", ['command' => $command]);

                // Updated regex to include name and imageUrl
                preg_match('/O:21:"App\\\\Jobs\\\\AddToCartJob":6:{.*?s:9:"productId";i:(\d+);.*?s:6:"userId";i:(\d+);.*?s:5:"price";d:(\d+(?:\.\d+)?);.*?s:4:"name";s:(\d+):"([^"]+)";.*?s:8:"imageUrl";s:(\d+):"([^"]*)";/s', $command, $matches);

                if (isset($matches[1]) && isset($matches[2]) && isset($matches[3])) {
                    $productId = (int) $matches[1];
                    $userId = (int) $matches[2];
                    $price = (float) $matches[3];
                    $name = $matches[5] ?? '';
                    $imageUrl = $matches[7] ?? '';

                    Log::info('Processing cart item', [
                        'userId' => $userId,
                        'productId' => $productId,
                        'price' => $price,
                        'name' => $name,
                        'imageUrl' => $imageUrl
                    ]);

                    try {
                        $this->addToCart($userId, $productId, $price, $name, $imageUrl);
                        Log::info('Successfully processed cart item', [
                            'userId' => $userId,
                            'productId' => $productId
                        ]);
                        $msg->ack();
                    } catch (Exception $e) {
                        Log::error('Error processing cart item', [
                            'userId' => $userId,
                            'error' => $e->getMessage()
                        ]);
                        $msg->nack(false, true);
                    }
                } else {
                    Log::error('Failed to extract data from command. Regex did not match.', [
                        'command' => $command,
                        'matches' => $matches ?? []
                    ]);
                    $msg->nack();
                }
            } else {
                Log::error('Invalid message format', ['messageData' => $messageData]);
                $msg->nack();
            }
        };

        $channel->basic_consume('add_to_cart', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * @throws Exception
     */
    protected function addToCart(int $userId, int $productId, float $price, string $name, string $imageUrl): void
    {
        Log::info("Adding to cart logic triggered", [
            'user_id' => $userId,
            'product_id' => $productId,
            'price' => $price,
            'name' => $name,
            'image_url' => $imageUrl
        ]);

        $this->cartItemService->addCartItem([
            "user_id" => $userId,
            "product_id" => $productId,
            "price" => $price,
            "name" => $name,
            "image_url" => $imageUrl,
        ]);
    }
}
