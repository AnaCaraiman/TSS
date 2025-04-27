<?php

namespace App\Console\Commands;

use App\Services\FavoriteService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CreateFavoriteCartCommand extends Command
{

    protected $signature = 'rabbitmq:create-favorite-cart';
    protected $description = 'Consumes messages from the create_favorite queue and creates favorite cart for users.';

    public function __construct(protected FavoriteService $favoriteService)
    {
        parent::__construct();
        Log::info("Favorite cart consumer command constructed successfully.");
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
        $channel->queue_declare('create_favorite', false, true, false, false);
        Log::info("Listening for messages on 'create_favorite' queue...");

        $callback = function (AMQPMessage $msg) {
            $messageData = json_decode($msg->getBody(), true);
            if (isset($messageData['data']['command'])) {
                $command = $messageData['data']['command'];
                preg_match('/i:(\d+);/', $command, $matches);
                if (isset($matches[1])) {
                    $userId = (int) $matches[1];
                    Log::info('Extracted UserId: ' . $userId);
                    try {
                        $this->createFavorite($userId);
                        Log::info('Successfully created favorite cart for user ID: ' . $userId);
                        $msg->ack();
                    } catch (Exception $e) {
                        Log::error('Error creating favorite cart for user ID: ' . $userId . ' - ' . $e->getMessage());
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

        $channel->basic_consume('create_favorite', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    /**
     *
     * @param int $userId
     * @throws Exception
     */
    protected function createFavorite(int $userId): void
    {
        try {
            Log::info("Favorite cart creation logic triggered for user_id $userId");
            $this->favoriteService->createFavorite($userId);
        } catch (Exception $e) {
            Log::error("Error while creating favorite cart: " . $e->getMessage());
            throw $e;
        }
    }
}
