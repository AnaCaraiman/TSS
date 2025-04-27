<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(1),
            'name' => $this->faker->name,
            'street' => $this->faker->streetName,
            'number' => $this->faker->buildingNumber,
            'additional_info' => $this->faker->optional()->sentence,
            'city' => $this->faker->city,
            'county' => $this->faker->state,
            'postcode' => $this->faker->numberBetween(10000, 99999),
            'status' => OrderStatus::PENDING, // default to PENDING
            'payment_type' => 'credit_card',  // or 'cash_on_delivery'
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
