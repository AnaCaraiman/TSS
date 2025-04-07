<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AuthRepository;
use App\Repositories\TokenRepository;
use Illuminate\Cookie\CookieJar;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Cookie;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepository,
        protected TokenRepository $tokenRepository) {}

    public function getTokens($user): array
    {
        return $this->tokenRepository->generateTokens($user);
    }

    public function getAccessToken($user): string
    {
        return $this->tokenRepository->generateAccessToken($user);
    }

    public function getCookie($tokens): Application|CookieJar|Cookie
    {
        return $this->tokenRepository->generateCookie($tokens);
    }

    public function registerUser($data): User|null
    {
        return $this->authRepository->createUser($data);
    }

}
