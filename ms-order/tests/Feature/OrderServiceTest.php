<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;
    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(OrderService::class);
    }
    /** @test */
    public function creates_an_order_successfully()
    {
        $orderData = [
            'user_id' => 1,
            'name' => 'string',
            'street' => 'string',
            'number' => 1,
            'additional_info' => 'string',
            'city' => 'string',
            'county' => 'string',
            'postcode' => 1,
            'payment_type' => 'credit_card',
            'price' => 1,
            'products' => [
                [
                    'id' => 1,
                    'name' => 'string',
                    'price' => 1,
                    'quantity' => 1,
                    'image_url' => 'https://example.com/product1.jpg',
                ],
                [
                    'id' => 2,
                    'name' => 'string',
                    'price' => 1,
                    'quantity' => 1,
                    'image_url' => 'https://example.com/product2.jpg',
                ],
            ]
        ];

        $order = $this->orderService->addOrder($orderData);

        $this->assertInstanceOf(Order::class, $order);

        $this->assertEquals(1, $order->user_id);
        $this->assertEquals('string', $order->name);
        $this->assertEquals('string', $order->street);
        $this->assertEquals(1, $order->number);
        $this->assertEquals('string', $order->city);
        $this->assertEquals('string', $order->county);
        $this->assertEquals(1, $order->postcode);
        $this->assertEquals('credit_card', $order->payment_type->value);
        $this->assertEquals(1, $order->price);
        $this->assertEquals('pending', $order->status->value);

        $this->assertCount(2, $order->products);
        $this->assertEquals(1, $order->products[0]['product_id']);
        $this->assertEquals('string', $order->products[0]['name']);
        $this->assertEquals(1, $order->products[0]['price']);
        $this->assertEquals(1, $order->products[0]['quantity']);
        $this->assertEquals('https://example.com/product1.jpg', $order->products[0]['image_url']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => 1,
            'name' => 'string',
            'price' => 1,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('order_product', 2);
    }

    #[Test]
    public function throws_exception_when_repository_returns_null()
    {
        $this->expectException(Exception::class);

        $missingOrderData = ['user_id' => 1];

        $this->orderService->addOrder($missingOrderData);

        $this->fail('Expected exception was not thrown.');
    }

    #[Test]
    public function it_retrieves_an_existing_order_successfully()
    {
        $order = Order::factory()->create();

         $foundOrder = $this->orderService->getOrder($order->id);

        $this->assertNotNull($foundOrder);
        $this->assertEquals($order->id, $foundOrder->id);
        $this->assertEquals($order->user_id, $foundOrder->user_id);
        $this->assertEquals($order->price, $foundOrder->price);
        $this->assertEquals($order->status->value, $foundOrder->status->value);
    }

    #[Test]
    public function it_fails_to_retrieve_nonexistent_order_and_throws_exception()
    {
        $nonExistentOrderId = 999999;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not found');

        $this->orderService->getOrder($nonExistentOrderId);

        $this->fail('Expected Exception was not thrown.');
    }

    #[Test]
    public function it_returns_paginated_orders_with_products()
    {
        $order1 = Order::factory()->create(['user_id' => 1]);
        $order2 = Order::factory()->create(['user_id' => 1]);

        DB::table('order_product')->insert([
            [
                'order_id' => $order1->id,
                'external_product_id' => 101,
                'name' => 'string',
                'price' => 1,
                'quantity' => 1,
                'image_url' => 'https://example.com/product1.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => $order2->id,
                'external_product_id' => 102,
                'name' => 'string',
                'price' => 1,
                'quantity' => 1,
                'image_url' => 'https://example.com/product2.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        $orders = $this->orderService->getOrders(1, 1);

        $this->assertCount(2, $orders);
        $this->assertEquals($order1->id, $orders[0]->id);
        $this->assertEquals($order2->id, $orders[1]->id);

        $this->assertNotEmpty($orders[0]->products);
        $this->assertEquals('string', $orders[0]->products[0]->name);
        $this->assertEquals($order1->id, $orders[0]->products[0]->order_id);

        $this->assertNotEmpty($orders[1]->products);
        $this->assertEquals('string', $orders[1]->products[0]->name);
        $this->assertEquals($order2->id, $orders[1]->products[0]->order_id);
    }

    #[Test]
    public function it_returns_empty_when_user_has_no_orders()
    {
        $userId = 2;
        $orders = $this->orderService->getOrders($userId, 1);

        $this->assertCount(0, $orders);
    }

    #[Test]
    public function it_cancels_an_order_successfully()
    {
        $order = Order::factory()->create([
            'created_at' => now()->subDays(10),
            'status' => OrderStatus::PENDING,
        ]);

        $this->orderService->cancelOrder($order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::CANCELED,
        ]);
    }

    #[Test]
    public function fails_to_cancel_an_order_older_than_two_weeks()
    {
        $order = Order::factory()->create([
            'created_at' => now()->subWeeks(3),
            'status' => OrderStatus::PENDING,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not cancelled');

        $this->orderService->cancelOrder($order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::PENDING,
        ]);
    }

    #[Test]
    public function it_fails_to_cancel_nonexistent_order()
    {
        $nonExistentOrderId = 999999;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not cancelled');

        $this->orderService->cancelOrder($nonExistentOrderId);

    }


}
