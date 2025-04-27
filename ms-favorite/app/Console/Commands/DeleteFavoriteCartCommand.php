<?php

namespace App\Console\Commands;

use App\Services\FavoriteService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class DeleteFavoriteCartCommand extends Command
{
    protected $signature = 'rabbitmq:delete-favorite-cart';
    protected $description = 'Delete favorite cart on deleting account';

    public function __construct(protected FavoriteService $favoriteService)
    {
        parent::__construct();
        Log::info("Delete favorite cart consumer command constructed successfully.");
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
        $channel->queue_declare('delete_favorite', false, true, false, false);
        Log::info("Listening for messages on 'delete_favorite' queue...");

        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            if (isset($messageData['data']['command'])) {
                $command = $messageData['data']['command'];
                preg_match('/i:(\d+);/', $command, $matches);
                if (isset($matches[1])) {
                    $userId = (int)$matches[1];
                    Log::info('Extracted UserId: ' . $userId);
                    try {
                        $this->deleteFavorite($userId);
                        Log::info('Successfully deleted favorite cart for user ID: ' . $userId);
                        $msg->ack();
                    } catch (Exception $e) {
                        Log::error('Error deleting favorite cart for user ID: ' . $userId . ' - ' . $e->getMessage());
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

        $channel->basic_consume('delete_favorite', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     * @throws Exception
     */
    private function deleteFavorite(int $userId): void
    {
        try {
            Log::info("Favorite cart delete logic triggered for user_id $userId");
            $this->favoriteService->deleteFavorite($userId);
        } catch (Exception $e) {
            Log::error("Error while deleting favorite cart: " . $e->getMessage());
            throw $e;
        }
    }
}
