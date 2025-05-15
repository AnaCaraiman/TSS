<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use Mockery;

class OrderServiceTest extends TestCase
{
    protected $orderRepository;
    protected $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepository = Mockery::mock(OrderRepository::class);
        $this->orderService = new OrderService($this->orderRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testAddOrderSuccess()
    {
        $data = ['user_id' => 1, 'total' => 100];
        $order = new Order($data);

        $this->orderRepository
            ->shouldReceive('createOrder')
            ->with($data)
            ->once()
            ->andReturn($order);

        $result = $this->orderService->addOrder($data);

        $this->assertEquals($order, $result);
    }

    public function testAddOrderFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not created');

        $data = ['user_id' => 1, 'total' => 100];

        $this->orderRepository
            ->shouldReceive('createOrder')
            ->with($data)
            ->once()
            ->andReturn(null);

        $this->orderService->addOrder($data);
    }

    /**
     * @throws Exception
     */
    public function testGetOrderSuccess()
    {
        $order = new Order(['id' => 1]);

        $this->orderRepository
            ->shouldReceive('getOrderById')
            ->with(1)
            ->once()
            ->andReturn($order);

        $result = $this->orderService->getOrder(1);

        $this->assertEquals($order, $result);
    }

    public function testGetOrderFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not found');

        $this->orderRepository
            ->shouldReceive('getOrderById')
            ->with(1)
            ->once()
            ->andReturn(null);

        $this->orderService->getOrder(1);
    }

    public function testGetOrders()
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->orderRepository
            ->shouldReceive('getOrdersByUserId')
            ->with(1, 1)
            ->once()
            ->andReturn($paginator);

        $result = $this->orderService->getOrders(1, 1);

        $this->assertEquals($paginator, $result);
    }

    /**
     * @throws Exception
     */
    public function testCancelOrderSuccess()
    {
        $this->orderRepository
            ->shouldReceive('cancelOrderById')
            ->with(1)
            ->once()
            ->andReturn(true);

        $this->orderService->cancelOrder(1);

        $this->assertTrue(true);
    }

    public function testCancelOrderFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not cancelled');

        $this->orderRepository
            ->shouldReceive('cancelOrderById')
            ->with(1)
            ->once()
            ->andReturn(false);

        $this->orderService->cancelOrder(1);
    }
}
