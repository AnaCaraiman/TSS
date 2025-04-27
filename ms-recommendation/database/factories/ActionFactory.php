<?php

namespace Database\Factories;

use App\Models\Action;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ActionFactory extends Factory
{
    protected $model = Action::class;

    public function definition(): array
    {
        $userIds = DB::table('actions')->pluck('user_id')->toArray();
        $productIds = DB::table('actions')->pluck('product_id')->toArray();

        return [
            'user_id' => $this->faker->numberBetween(1,150),
            'product_id' => $this->faker->numberBetween(2,151),
            'action_id' => $this->faker->numberBetween(1, 4),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
