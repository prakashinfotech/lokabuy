<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->sendEmailVerificationNotification();

        $token = $user->createToken('access-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
                'email_verified' => false,
                'message' => 'Account created. Please verify your email address.',
            ],
            'error' => null,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $throttleKey = 'login:'.Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success' => false,
                'data' => null,
                'error' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 900);

            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Invalid credentials',
            ], 401);
        }

        if (! $user->is_active) {
            RateLimiter::hit($throttleKey, 900);

            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Your account has been deactivated.',
            ], 403);
        }

        RateLimiter::clear($throttleKey);
        $user->tokens()->where('name', 'access-token')->delete();

        $accessToken = $user->createToken('access-token')->plainTextToken;
        $refreshToken = $user->createToken('refresh-token', ['refresh'])->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
                'token' => $accessToken,
                'refresh_token' => $refreshToken,
            ],
            'error' => null,
        ]);
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'error' => null,
        ]);
    }

    public function refresh(): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Invalid refresh token',
            ], 401);
        }

        $user->tokens()->where('name', 'access-token')->delete();
        $accessToken = $user->createToken('access-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => ['token' => $accessToken],
            'error' => null,
        ]);
    }
}
