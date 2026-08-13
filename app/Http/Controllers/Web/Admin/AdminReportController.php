<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Notifications\ListingRemovedNotification;
use App\Services\ListingService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $reports = Report::pending()
            ->with(['listing.images', 'reporter'])
            ->latest('id')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function action(Report $report): RedirectResponse
    {
        if ($report->listing) {
            $listing = $report->listing;
            $this->listingService->delete($listing);
            $this->notificationService->send($listing->user, new ListingRemovedNotification($listing));

            Report::where('listing_id', $report->listing_id)
                ->where('status', 'pending')
                ->update(['status' => 'actioned']);
        } else {
            $report->update(['status' => 'actioned']);
        }

        return back()->with('success', 'Listing removed and report resolved.');
    }

    public function dismiss(Report $report): RedirectResponse
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('success', 'Report dismissed.');
    }
}
