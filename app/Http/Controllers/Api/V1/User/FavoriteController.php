<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Listing;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(private readonly FavoriteService $favoriteService) {}

    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with(['listing' => fn ($q) => $q->with(['user', 'category', 'subcategory', 'images'])])
            ->latest('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => FavoriteResource::collection($favorites)->response()->getData(true),
            'error' => null,
        ]);
    }

    public function toggle(Request $request, Listing $listing): JsonResponse
    {
        $favorited = $this->favoriteService->toggle($request->user(), $listing);

        return response()->json([
            'success' => true,
            'data' => ['favorited' => $favorited],
            'error' => null,
        ]);
    }
}
