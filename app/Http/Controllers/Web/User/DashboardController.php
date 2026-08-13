<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;
use App\Services\ListingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly ListingService $listingService) {}

    public function index(): View
    {
        $user = auth()->user();
        $stats = $this->listingService->getUserStats($user);
        $recentListings = $this->listingService->getRecentListings($user);

        return view('dashboard.index', compact('stats', 'recentListings'));
    }
}
