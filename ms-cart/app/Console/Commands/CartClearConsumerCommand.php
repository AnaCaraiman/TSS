<?php

namespace App\Console\Commands;

use App\Services\CartService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CartClearConsumerCommand extends Command
{
    protected $signature = 'rabbitmq:consume-cart-clear';
    protected $description = 'Consumes messages from the clear_cart queue and deletes the items from users cart.';

    public function __construct(protected CartService $cartService)
    {
        parent::__construct();
        Log::info("Cart consumer deletion command constructed successfully.");
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
        $channel->queue_declare('clear_cart', false, true, false, false);
        Log::info("Listening for messages on 'clear_cart' queue...");
        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            if (isset($messageData['data']['command'])) {
                $command = $messageData['data']['command'];
                preg_match('/i:(\d+);/', $command, $matches);
                if (isset($matches[1])) {
                    $userId = (int)$matches[1];
                    Log::info('Extracted UserId: ' . $userId);
                    try {
                        $this->clearCart($userId);
                        Log::info('Successfully cleared cart for user ID: ' . $userId);
                        $msg->ack();
                    } catch (Exception $e) {
                        Log::error('Error clearing cart for user ID: ' . $userId . ' - ' . $e->getMessage());
                        $msg->nack(false, true);
                    }
                } else {
                    Log::error('Failed to extract userId from command: ' . $command);
                    $msg->nack();
                }
            } else {
                Log::error('Invalid message format. Command not found in message data.');
                $msg->nack();
            }
        };
        $channel->basic_consume('clear_cart', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function clearCart(int $userId): void {
        try{
            Log::info("Cart clearing logic triggered for user_id $userId");
            $this->cartService->clearCart($userId);
        }
        catch(Exception $e){
            Log::error("Error while clearing cart: " . $e->getMessage());
            throw $e;
        }
    }
}
