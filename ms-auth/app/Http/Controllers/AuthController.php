<?php

namespace App\Http\Controllers;



use App\Events\UserDeletedEvent;
use App\Events\UserRegisteredEvent;
use App\Http\Resources\Auth\AuthLoginResource;
use App\Http\Resources\Auth\AuthLogoutResource;
use App\Http\Resources\Auth\AuthRefreshResource;
use App\Http\Resources\Auth\AuthRegisterResource;
use App\Models\User;
use App\Services\AuthService;
use App\Validators\RegisterValidators;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;



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

        Event::dispatch(new UserRegisteredEvent($user));

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

    public function uploadProfilePicture(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$request->hasFile('profile_picture')) {
                if ($user->profile_picture_url) {
                    $this->deleteProfilePicture($user);
                    return response()->json(['message' => 'Profile picture removed successfully']);
                }
                return response()->json(['message' => 'No profile picture was set'], 200);
            }

            $request->validate([
                'profile_picture' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $file = $request->file('profile_picture');
            $filename = 'profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = 'profile_pictures/' . $filename;

            if ($user->profile_picture_url) {
                $this->deleteProfilePicture($user);
            }

            $result = Storage::disk('s3')->put($path, file_get_contents($file));

            if (!$result) {
                throw new Exception('Failed to upload file to S3.');
            }

            $url = Storage::disk('s3')->url($path);

            $user->profile_picture_url = $url;
            $user->save();

            return response()->json([
                'message' => 'Profile picture updated successfully',
                'url' => $url
            ]);

        } catch (Exception $e) {
            Log::error('❌ Error uploading profile picture', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function deleteProfilePicture(User $user): void
    {
        if (!$user->profile_picture_url) {
            $user->profile_picture_url = null;
            $user->save();
            return;
        }

        $parsed = parse_url($user->profile_picture_url);
        $oldPath = ltrim($parsed['path'] ?? '', '/');

        if ($oldPath && Storage::disk('s3')->exists($oldPath)) {
            Storage::disk('s3')->delete($oldPath);
        } else {
            Log::warning('File does not exist on S3 or path is invalid', ['s3_path' => $oldPath]);
        }

        $user->profile_picture_url = null;
        $user->save();
    }


    public function changePassword(Request $request): JsonResponse {
        try {
            $user = $request->user();
            if (!$user->password) {
                throw new Exception("User does not have a password");
            }

            $oldPassword = $request->input('old_password');
            $newPassword = $request->input('new_password');

            if(Hash::check($oldPassword, $user->password)) {
                $this->authService->changePassword($user, $newPassword);
            }
            else{
                throw new Exception("Wrong old password");
            }

            return response()->json([
                'message' => 'Password changed successfully'
            ]);
        }
        catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }


    public function getAllUsers(): JsonResponse {
        return response()->json([
            'message' => 'Get all users',
            'users' => User::all()
        ],201);
    }



}
