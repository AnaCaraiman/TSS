<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\JsonResponse;

class AuthRefreshResource
{
    public static function success(string $accessToken, string $cookie): JsonResponse
    {
        return response()->json([
            'accessToken' => $accessToken,
            'message' => 'Access token refreshed successfully.',
        ], 200)->withCookie($cookie);
    }
}