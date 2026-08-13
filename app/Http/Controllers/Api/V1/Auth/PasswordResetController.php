<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(['email' => $request->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Unable to send reset link. Please try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Password reset link sent to your email.'],
            'error' => null,
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Invalid or expired reset token.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Password reset successfully.'],
            'error' => null,
        ]);
    }
}
