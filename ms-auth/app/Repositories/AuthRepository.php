<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function createUser(array $data): User
    {
        return User::create([
            'last_name' => $data['last_name'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ? Hash::make($data['password']) : null,
            'phone_number' => $data['phone_number'] ?? null,
            'profile_picture_url' => $data['profile_picture_url'] ?? null,
        ]);
    }
}
