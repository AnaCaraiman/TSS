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

class AddToCartJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $productId;
    public int $userId;
    public float $price;
    public string $name;
    public string $imageUrl;

    public function __construct($userId,$productId,$price,$name,$imageUrl)
    {

        $this->userId = $userId;
        $this->productId = $productId;
        $this->price = $price;
        $this->name = $name;
        $this->imageUrl = $imageUrl;
        Log::info('Adding to cart job',
            [$this->userId,
                $this->productId,
                $this->price,
                $this->name,
                $this->imageUrl]);
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
        $messageBody = json_encode([
            'user_id' => $this->userId,
            'productId' => $this->productId,
            'price' => $this->price,
            'name' => $this->name,
            'imageUrl' => $this->imageUrl]);

        $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($message, '', 'add_to_cart');

        $channel->close();
    }
}
