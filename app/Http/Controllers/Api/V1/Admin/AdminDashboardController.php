<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'active_listings' => Listing::where('status', 'active')->count(),
                'pending_listings' => Listing::pending()->count(),
                'pending_reports' => Report::pending()->count(),
                'new_users_last_7days' => User::where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'error' => null,
        ]);
    }
}
