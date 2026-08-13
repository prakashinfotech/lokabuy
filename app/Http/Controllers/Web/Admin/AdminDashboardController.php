<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'pending_listings' => Listing::pending()->count(),
            'pending_reports' => Report::pending()->count(),
            'new_users_last_7days' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
