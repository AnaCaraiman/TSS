<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController
{
    private string $authServiceUrl;
    public function __construct(){
        $this->authServiceUrl = config('services.ms_auth.url');
    }

    public function login(Request $request): JsonResponse
    {
        $response = Http::post($this->authServiceUrl . '/api/auth/login',$request->all());


        return response()->json(json_decode($response->getBody()->getContents(),true),$response->status());
    }

    public function register(Request $request): JsonResponse{
        $response = Http::post($this->authServiceUrl . '/api/auth/register',$request->all());


        return response()->json(json_decode($response->getBody()->getContents(),true),$response->status());
    }

    public function logout(Request $request): JsonResponse {
        try {
            $accessToken = $request->bearerToken();

            if (!$accessToken) {
               throw new Exception("Unauthorized");
            }
            $response = Http::withToken(trim($accessToken))->post($this->authServiceUrl . '/api/auth/logout', $request->all());
            return response()->json(json_decode($response->getBody()->getContents(), true));
        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $accessToken = $request->bearerToken();

            if (!$accessToken) {
                throw new Exception("Unauthorized");
            }

            $response = Http::withToken(trim($accessToken))->post($this->authServiceUrl . '/api/auth/refresh-token', $request->all());
            return response()->json(json_decode($response->getBody()->getContents(), true));
        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function getUserId(Request $request): int
    {
        $accessToken = $request->bearerToken();
        if (!$accessToken) {
            throw new Exception("Unauthorized");
        }
        $response = Http::withToken(trim($accessToken))->get($this->authServiceUrl . '/api/auth/user');
        $userData = json_decode($response->getBody()->getContents(), true);
        return $userData['user_id'];

    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            throw new Exception("Unauthorized");
        }

        $response = Http::withToken(trim($accessToken))->delete($this->authServiceUrl . '/api/auth/', $request->all());
        return response()->json(json_decode($response->getBody()->getContents(), true));

    }

    public function getUserInfo(Request $request): JsonResponse {
        $accessToken = $request->bearerToken();
        if (!$accessToken) {
            throw new Exception("Unauthorized");
        }
        $response = Http::withToken(trim($accessToken))->get($this->authServiceUrl . '/api/auth/userInfo');
        return response()->json(json_decode($response->getBody()->getContents(), true));
    }

}
