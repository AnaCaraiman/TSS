<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class AuthRegisterResource
{
    public static function invalidData(array $errors): JsonResponse
    {
        return response()->json([
            'message' => 'Invalid data.',
            'errors' => $errors,
        ], 401);
    }

    public static function success(array $user, string $accessToken, Cookie $cookie): JsonResponse
    {
        return response()->json([
            'user' => $user,
            'accessToken' => $accessToken,
            'message' => 'Account created successfully.',
        ], 201)->withCookie($cookie);
    }
} 