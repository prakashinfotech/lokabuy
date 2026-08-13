<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Notifications\ListingRemovedNotification;
use App\Services\ListingService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class AdminReportController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly NotificationService $notificationService,
    ) {}

    public function index(): JsonResponse
    {
        $reports = Report::pending()
            ->with(['listing.user', 'listing.category', 'listing.subcategory', 'listing.images', 'reporter'])
            ->latest('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ReportResource::collection($reports)->response()->getData(true),
            'error' => null,
        ]);
    }

    public function action(Report $report): JsonResponse
    {
        if ($report->listing) {
            $listing = $report->listing;
            $this->listingService->delete($listing);
            $this->notificationService->send($listing->user, new ListingRemovedNotification($listing));

            // Resolve all other pending reports for the same listing
            Report::where('listing_id', $report->listing_id)
                ->where('status', 'pending')
                ->update(['status' => 'actioned']);
        } else {
            $report->update(['status' => 'actioned']);
        }

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Listing removed and report resolved.'],
            'error' => null,
        ]);
    }

    public function dismiss(Report $report): JsonResponse
    {
        $report->update(['status' => 'dismissed']);

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Report dismissed.'],
            'error' => null,
        ]);
    }
}
