<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function index(Request $request): JsonResponse
    {
        $users = User::when($request->input('q'), function ($query, $q) {
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        })
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)->response()->getData(true),
            'error' => null,
        ]);
    }

    public function deactivate(User $user): JsonResponse
    {
        $updated = $this->adminService->deactivateUser($user);

        return response()->json([
            'success' => true,
            'data' => new UserResource($updated),
            'error' => null,
        ]);
    }
}
