<?php

namespace App\Providers;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQServiceProvider
{
    public function register()
    {
        $this->app->singleton(AMQPStreamConnection::class, function ($app) {
            return new AMQPStreamConnection(
                env('RABBITMQ_HOST', '127.0.0.1'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest')
            );
        });
    }

    public function boot()
    {
    }

}
