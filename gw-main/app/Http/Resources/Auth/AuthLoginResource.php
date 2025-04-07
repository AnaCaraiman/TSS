<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class AuthLoginResource
{
    public static function invalidCredentials(): JsonResponse
    {
        return response()->json([
            'message' => 'Wrong email or password.'
        ], 401);
    }

    public static function success(array $user, string $accessToken, Cookie $cookie): JsonResponse
    {
        return response()->json([
            'user' => $user,
            'accessToken' => $accessToken,
            'message' => 'User logged in successfully.',
        ], 201)->withCookie($cookie);
    }
} 