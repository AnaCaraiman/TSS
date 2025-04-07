<?php

namespace App\Http\Controllers;



use App\Events\UserDeletedEvent;
use App\Events\UserRegisteredEvent;
use App\Http\Resources\Auth\AuthLoginResource;
use App\Http\Resources\Auth\AuthLogoutResource;
use App\Http\Resources\Auth\AuthRefreshResource;
use App\Http\Resources\Auth\AuthRegisterResource;
use App\Services\AuthService;
use App\Validators\RegisterValidators;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService) {}

    public function login(Request $request): JsonResponse
    {
        if (!Auth::attempt($request->all())) {
            return AuthLoginResource::invalidCredentials();
        }

        $user = Auth::user();
        $tokens = $this->authService->getTokens($user);
        $cookie = $this->authService->getCookie($tokens);

        return AuthLoginResource::success(
            $user->toArray(),
            $tokens['accessToken'],
            $cookie
        );
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            RegisterValidators::rules,
            RegisterValidators::messages
        );

        if ($validator->fails()) {
            return AuthRegisterResource::invalidData(collect($validator->errors()->all()));
        }

        $user = $this->authService->registerUser($request->all());
        $tokens = $this->authService->getTokens($user);
        $cookie = $this->authService->getCookie($tokens);

        Event::dispatch(new UserRegisteredEvent($user->id));

        return AuthRegisterResource::success(
            $user->toArray(),
            $tokens['accessToken'],
            $cookie
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        $cookie = cookie()->forget('refreshToken');

        return AuthLogoutResource::success($cookie);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = Auth::user();
        $request->user()->tokens()->where('name', 'access_token')->delete();

        $accessToken = $this->authService->getAccessToken($user);
        $cookie = $request->cookie('refreshToken');

        return AuthRefreshResource::success($accessToken, $cookie);
    }

    public function getUserId(): JsonResponse{
        $user = Auth::user();
        return response()->json(['user_id'=>$user->id],201);
    }

    public function getUser():JsonResponse{
        $user = Auth::user();
        return response()->json(['user'=>$user],201);
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $user = Auth::user();
        Event::dispatch(new UserDeletedEvent($user->id));
        $request->user()->tokens()->delete();
        $user->delete();

        $cookie = cookie()->forget('refreshToken');



        return response()->json([
            'message' => 'Account deleted successfully'
        ],201)->withCookie($cookie);
    }
}
