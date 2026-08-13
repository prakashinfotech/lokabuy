<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectListingRequest;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminListingController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function pending(): JsonResponse
    {
        $listings = Listing::pending()
            ->with(['user', 'category', 'subcategory', 'images'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ListingResource::collection($listings)->response()->getData(true),
            'error' => null,
        ]);
    }

    public function approve(Listing $listing): JsonResponse
    {
        $listing = $this->adminService->approve($listing);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }

    public function reject(RejectListingRequest $request, Listing $listing): JsonResponse
    {
        $listing = $this->adminService->reject($listing, $request->reason);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }

    public function setFeatured(Request $request, Listing $listing): JsonResponse
    {
        $request->validate([
            'is_featured' => ['required', 'boolean'],
            'featured_until' => ['nullable', 'date', 'after:now'],
        ]);

        $listing = $this->adminService->setFeatured(
            $listing,
            (bool) $request->is_featured,
            $request->featured_until
        );

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }
}
