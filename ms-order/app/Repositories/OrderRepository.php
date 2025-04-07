<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            Log::info($productData);
            $product = Product::firstOrCreate(
                ['id' => $productData['id']],
                [
                    'id' => $productData['id'],
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'image_url' => $productData['image_url'] ?? null,
                    'quantity' => $productData['stock'] ?? 0,
                ]
            );

            $order->products()->attach($product->id, [
                'quantity' => $productData['quantity'],
            ]);
        }

        Log::info("Order created with id $order->id");

        $order->load('products');

        $order->products = $order->products->map(function ($product) {
            return [
                'product_id' => $product->pivot->product_id ?? $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'image_url'  => $product->image_url,
                'quantity'   => $product->pivot->quantity,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
            ];
        });

        $order->setRelation('products', collect($order->products));

        return $order;

    }



    public function getOrdersByUserId(string $userId, int $page = 1, int $perPage = 10): LengthAwarePaginator {
        return Order::with('products')
            ->where('user_id', $userId)
            ->paginate($perPage, ['*'], 'page', $page);
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
