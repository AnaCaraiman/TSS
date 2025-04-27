<?php

namespace App\Console\Commands;

use App\Services\ItemService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class DeleteFromFavoriteCartCommand extends Command
{
    protected $signature = 'rabbitmq:delete-from-favorite-cart-command';
    protected $description = 'Remove item from favorites.';

    public function __construct(protected itemService $itemService)
    {
        parent::__construct();
        Log::info("Remove item from favorites command constructed successfully.");
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
        $channel->queue_declare('remove_from_favorite', false, true, false, false);
        Log::info("Listening for messages on 'remove_from_favorite' queue...");

        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            if (isset($messageData['data']['command'])) {
                $command = $messageData['data']['command'];
                preg_match('/s:9:"productId";i:(\d+);.*?s:6:"userId";i:(\d+);/', $command, $matches);
                if (isset($matches[1]) && isset($matches[2])) {
                    $productId = (int)$matches[1];
                    $userId = (int)$matches[2];
                    Log::info('Extracted UserId: ' . $userId);
                    try {
                        $this->removeFromFavorites($userId,$productId);
                        Log::info('Successfully removed item from favorites for user ID: ' . $userId);
                        $msg->ack();
                    } catch (Exception $e) {
                        Log::error('Error removing from favorites for user ID: ' . $userId . ' - ' . $e->getMessage());
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
        $channel->basic_consume('remove_from_favorite', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * @throws Exception
     */
    private function removeFromFavorites(int $userId, int $productId): void
    {
        try {
            Log::info("Remove from favorites logic triggered for user_id $userId");
            $this->itemService->deleteItem(['user_id' => $userId,'product_id' => $productId]);
        } catch (Exception $e) {
            Log::error("Error while removing from favorites: " . $e->getMessage());
            throw $e;
        }
    }
}
