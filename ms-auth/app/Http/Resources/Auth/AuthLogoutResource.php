<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class AuthLogoutResource
{
    public static function success(Cookie $cookie): JsonResponse
    {
        return response()->json([
            'message' => 'User logged out successfully.',
        ])->withCookie($cookie);
    }
}
