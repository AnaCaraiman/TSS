<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->unique()->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
