<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class DeleteCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Handle the job.
     *
     * @param AMQPStreamConnection $connection
     * @throws Exception
     */
    public function handle(AMQPStreamConnection $connection): void
    {
        $channel = $connection->channel();

        $messageBody = json_encode(['user_id' => $this->userId]);
        $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($message, '', 'delete_cart');

        $channel->close();
    }

}
