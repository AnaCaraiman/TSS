<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use Exception;

class OrderServiceTest extends TestCase
{
    private OrderRepository|MockObject $orderRepository;
    private OrderService $orderService;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->orderService = new OrderService($this->orderRepository);
    }

    /** @test
     * @throws Exception
     */
    public function it_adds_an_order_successfully()
    {
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('createOrder')
            ->willReturn($order);

        $result = $this->orderService->addOrder(['dummy' => 'data']);

        $this->assertInstanceOf(Order::class, $result);
    }

    /** @test */
    public function it_throws_exception_when_order_creation_fails()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('createOrder')
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not created');

        $this->orderService->addOrder(['dummy' => 'data']);
    }

    /** @test
     * @throws Exception
     */
    public function it_returns_order_by_id_successfully()
    {
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('getOrderById')
            ->willReturn($order);

        $result = $this->orderService->getOrder(1);

        $this->assertInstanceOf(Order::class, $result);
    }

    /** @test */
    public function it_throws_exception_when_order_not_found()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('getOrderById')
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not found');

        $this->orderService->getOrder(1);
    }

    /** @test
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function it_returns_paginated_orders()
    {
        $paginatorMock = $this->createMock(LengthAwarePaginator::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('getOrdersByUserId')
            ->willReturn($paginatorMock);

        $result = $this->orderService->getOrders(1, 1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    /** @test
     * @throws Exception
     */
    public function it_cancels_an_order_successfully()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('cancelOrderById')
            ->willReturn(true);

        $this->orderService->cancelOrder(1);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_exception_when_cancel_fails()
    {
        $this->orderRepository
            ->expects($this->once())
            ->method('cancelOrderById')
            ->willReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Order not cancelled');

        $this->orderService->cancelOrder(1);
    }
}
