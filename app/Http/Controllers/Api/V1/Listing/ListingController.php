<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingRequest;
use App\Http\Resources\ListingCollection;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListingService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly SearchService $searchService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $paginator = $this->searchService->search($request);

        return response()->json([
            'success' => true,
            'data' => new ListingCollection($paginator),
            'error' => null,
        ]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $term = (string) $request->input('q', '');
        $cacheKey = 'autocomplete:'.md5($term);

        $results = Cache::remember($cacheKey, 60, fn () => $this->searchService->autocomplete($term));

        return response()->json([
            'success' => true,
            'data' => $results,
            'error' => null,
        ]);
    }

    public function show(Listing $listing): JsonResponse
    {
        $listing = Cache::remember("listing:{$listing->slug}", 300, function () use ($listing) {
            return $listing->load(['user', 'category', 'subcategory', 'images']);
        });

        $this->listingService->incrementViewCount($listing);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }

    public function store(StoreListingRequest $request): JsonResponse
    {
        $listing = $this->listingService->create($request->user(), $request->validated());
        $listing->load(['user', 'category', 'subcategory', 'images']);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ], 201);
    }

    public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
    {
        $this->authorize('update', $listing);

        $listing = $this->listingService->update($listing, $request->validated());
        $listing->load(['user', 'category', 'subcategory', 'images']);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }

    public function destroy(Listing $listing): JsonResponse
    {
        $this->authorize('delete', $listing);

        $this->listingService->delete($listing);

        return response()->json([
            'success' => true,
            'data' => null,
            'error' => null,
        ], 204);
    }

    public function markAsSold(Listing $listing): JsonResponse
    {
        $this->authorize('markAsSold', $listing);

        $listing = $this->listingService->markAsSold($listing);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }

    public function renew(Listing $listing): JsonResponse
    {
        $this->authorize('renew', $listing);

        $listing = $this->listingService->renew($listing);

        return response()->json([
            'success' => true,
            'data' => new ListingResource($listing),
            'error' => null,
        ]);
    }
}
