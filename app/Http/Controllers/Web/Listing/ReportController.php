<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Listing;
use App\Models\Report;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function store(StoreReportRequest $request, Listing $listing): JsonResponse
    {
        $this->authorize('create', [Report::class, $listing]);

        $report = $this->adminService->storeReport($request->user(), $listing, $request->reason);

        if ($report === null) {
            return response()->json([
                'success' => false,
                'data' => null,
                'error' => 'You have already reported this listing.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'data' => ['message' => 'Listing reported successfully.'],
            'error' => null,
        ]);
    }
}
