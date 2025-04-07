<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepository) {}

    /**
     * @throws Exception
     */
    public function addOrder(array $data): ?Order {
        $order = $this->orderRepository->createOrder($data);
        if(!$order) {
            throw new Exception("Order not created");
        }
        return $order;
    }

    /**
     * @throws Exception
     */
    public function getOrder(int $id): ?object {
        $order =  $this->orderRepository->getOrderById($id);
        if (!$order) {
            throw new Exception("Order not found");
        }
        return $order;
    }

    public function getOrders(int $userId, int $page) : LengthAwarePaginator {
        return $this->orderRepository->getOrdersByUserId($userId, $page);
    }

    /**
     * @throws Exception
     */
    public function cancelOrder(int $id): void {
        if(!$this->orderRepository->cancelOrderById($id)) {
            throw new Exception("Order not cancelled");
        }
    }

}
