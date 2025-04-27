<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RemoveFromFavoriteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $productId;
    public int $userId;
    public function __construct($userId, $productId) {
        $this->productId = $productId;
        $this->userId = $userId;
        Log::info('Removing from favorite job', [$this->userId, $this->productId]);
    }

    /**
     * Handle the job.
     *
     * @param AMQPStreamConnection $connection
     * @throws Exception
     */
    public function handle(AMQPStreamConnection $connection): void {
        $channel = $connection->channel();
        $messageBody = json_encode([
            'user_id' => $this->userId,
            'productId' => $this->productId
        ]);

        $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($message, '', 'remove_from_favorite');

        $channel->close();
    }

}
