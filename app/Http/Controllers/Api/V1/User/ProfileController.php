<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()),
            'error' => null,
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $request->user()->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->fresh()),
            'error' => null,
        ]);
    }

    public function uploadAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $avatarUrl = $this->imageService->storeAvatar($user->id, $request->file('avatar'));

        $user->update(['avatar_url' => $avatarUrl]);

        return response()->json([
            'success' => true,
            'data' => ['avatar_url' => $avatarUrl],
            'error' => null,
        ]);
    }
}
