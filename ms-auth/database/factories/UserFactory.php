<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->numerify('07########'),
            'password' => Hash::make($this->faker->password),
            'profile_picture_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
