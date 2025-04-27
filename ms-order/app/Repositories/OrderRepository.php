<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function createOrder(array $data): ?Order
    {
        $order = Order::create([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'street' => $data['street'],
            'number' => $data['number'],
            'additional_info' => $data['additional_info'] ?? null,
            'city' => $data['city'],
            'county' => $data['county'],
            'postcode' => $data['postcode'],
            'status' => OrderStatus::PENDING,
            'payment_type' => $data['payment_type'],
            'price' => $data['price'],
        ]);

        foreach ($data['products'] as $productData) {
            DB::table('order_product')->insert([
                'order_id' => $order->id,
                'product_id' => null,
                'external_product_id' => $productData['id'],
                'name' => $productData['name'],
                'price' => $productData['price'],
                'image_url' => $productData['image_url'] ?? null,
                'quantity' => $productData['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        $order->products = collect($data['products'])->map(function ($product) {
            return [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $product['quantity'],
                'image_url' => $product['image_url'] ?? null,
            ];
        });

        return $order;
    }


    public function getOrdersByUserId(int $userId, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $orders = Order::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $orderIds = $orders->pluck('id')->toArray();

        $productSnapshots = DB::table('order_product')
            ->whereIn('order_id', $orderIds)
            ->select('order_id', 'external_product_id as product_id', 'name', 'price', 'quantity', 'image_url')
            ->get()
            ->groupBy('order_id');

        foreach ($orders as $order) {
            $order->products = $productSnapshots->get($order->id)?->values() ?? collect();
        }

        return $orders;
    }


    public function getOrderById(string $id): ?Order {
        return Order::with('products')->find($id);
    }

    public function cancelOrderById(string $id): bool {
        return DB::table('orders')
                ->where('id', $id)
                ->where('created_at', '>=', now()->subWeeks(2))
                ->update([
                    'status' => OrderStatus::CANCELED,
                ]) > 0;
    }




}
