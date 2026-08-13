<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingImageRequest;
use App\Http\Resources\ListingImageResource;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;

class ListingImageController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function store(StoreListingImageRequest $request, Listing $listing): JsonResponse
    {
        $this->authorize('uploadImage', $listing);

        if (! $this->imageService->canAddImages($listing)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => "Maximum {$this->imageService->maxPerListing()} images allowed per listing.",
            ], 422);
        }

        $image = $this->imageService->store($listing, $request->file('image'));

        return response()->json([
            'success' => true,
            'data' => new ListingImageResource($image),
            'error' => null,
        ], 201);
    }

    public function destroy(Listing $listing, ListingImage $image): JsonResponse
    {
        $this->authorize('deleteImage', $listing);

        if ($image->listing_id !== $listing->id) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'Not found',
            ], 404);
        }

        $this->imageService->delete($image);

        return response()->json([
            'success' => true,
            'data' => null,
            'error' => null,
        ], 204);
    }
}
