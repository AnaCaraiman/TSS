<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use App\Events\StockChangeEvent;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/ms-order', [OrderController::class, 'makeOrder']);
        Route::get('/api/ms-order', [OrderController::class, 'getOrders']);
        Route::get('/api/ms-order/{orderId}', [OrderController::class, 'getOrder']);
        Route::put('/api/ms-order/cancel/{orderId}', [OrderController::class, 'cancelOrder']);
    }

    #[Test]
    public function it_creates_an_order_successfully()
    {
        Event::fake();

        $payload = [
            'user_id' => 1,
            'name' => 'John Doe',
            'street' => 'Main St',
            'number' => 42,
            'city' => 'New York',
            'county' => 'NY',
            'postcode' => 10001,
            'payment_type' => 'credit_card',
            'price' => 99.99,
            'products' => [
                [
                    'id' => 1,
                    'name' => 'Product 1',
                    'price' => 99.99,
                    'quantity' => 1,
                ]
            ]
        ];

        $response = $this->postJson('/api/ms-order', $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order created successfully']);

        Event::assertDispatched(StockChangeEvent::class);
    }

    #[Test]
    public function it_returns_400_on_validation_failure()
    {
        $payload = [];

        $response = $this->postJson('/api/ms-order', $payload);

        $response->assertStatus(400);
        $this->assertStringContainsString('The user ID is required.', $response->json('error'));
    }

    #[Test]
    public function it_gets_a_single_order()
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/ms-order/$order->id");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order retrieved successfully']);
        $this->assertEquals($order->id, $response->json('order.id'));
    }

    #[Test]
    public function it_gets_paginated_orders_for_user()
    {
        Order::factory()->count(3)->create(['user_id' => 5]);

        $response = $this->getJson('/api/ms-order?user_id=5&page=1');

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'User orders retrieved successfully']);
        $this->assertCount(3, $response->json('orders'));
    }

    #[Test]
    public function it_cancels_an_order_successfully()
    {
        $order = Order::factory()->create();

        $response = $this->putJson("/api/ms-order/cancel/$order->id");

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Order cancelled successfully']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELED,
        ]);
    }

    #[Test]
    public function it_returns_400_if_cancel_fails()
    {
        $order = Order::factory()->create([
            'created_at' => now()->subWeeks(3),
        ]);

        $response = $this->putJson("/api/ms-order/cancel/$order->id");

        $response->assertStatus(400);
        $this->assertStringContainsString('Order not cancelled', $response->json('message'));
    }
}
