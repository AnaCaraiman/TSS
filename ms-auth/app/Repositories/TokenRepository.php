<?php

namespace App\Repositories;

use Illuminate\Cookie\CookieJar;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Cookie;

class TokenRepository
{
    public function generateAccessToken($user) {
        $atExpireTime = now()->addMinutes(config('sanctum.expiration'));
        $accessToken = $user->createToken('access_token', ['access-api'], $atExpireTime);

        return $accessToken->plainTextToken;
    }

    public function generateRefreshToken($user) {
        $rtExpireTime = now()->addMinutes(config('sanctum.rt_expiration'));
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], $rtExpireTime);

        return $refreshToken->plainTextToken;
    }
    public function generateTokens($user): array
    {
        return [
            'accessToken' => $this->generateAccessToken($user),
            'refreshToken' => $this->generateRefreshToken($user),
        ];
    }

    public function generateCookie($tokens): Application|CookieJar|Cookie
    {
        $rtExpireTime = config('sanctum.rt_expiration');
        return cookie('refreshToken', $tokens['refreshToken'], $rtExpireTime, secure: true);
    }

}
