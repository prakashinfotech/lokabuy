<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Services\ListingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly ListingService $listingService) {}

    public function index(Request $request): JsonResponse
    {
        $stats = $this->listingService->getUserStats($request->user());

        return response()->json([
            'success' => true,
            'data' => [
                'active_listings' => $stats['active_listings'],
                'sold_listings' => $stats['sold_listings'],
                'total_views' => $stats['total_views'],
                'unread_messages' => 0,
            ],
            'error' => null,
        ]);
    }

    public function listings(Request $request): JsonResponse
    {
        $listings = $this->listingService->getUserListings($request->user());

        return response()->json([
            'success' => true,
            'data' => ListingResource::collection($listings)->response()->getData(true),
            'error' => null,
        ]);
    }
}
