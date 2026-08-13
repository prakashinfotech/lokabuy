<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectListingRequest;
use App\Models\Listing;
use App\Services\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminListingController extends Controller
{
    public function __construct(private readonly AdminService $adminService) {}

    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        $listings = Listing::when($status === 'pending', fn ($q) => $q->pending())
            ->when($status === 'approved', fn ($q) => $q->where('approval_status', 'approved'))
            ->when($status === 'rejected', fn ($q) => $q->where('approval_status', 'rejected'))
            ->with(['user', 'category', 'subcategory', 'images'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.listings.index', compact('listings', 'status'));
    }

    public function approve(Listing $listing): RedirectResponse
    {
        $this->adminService->approve($listing);

        return back()->with('success', "Listing \"{$listing->title}\" approved.");
    }

    public function reject(RejectListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->adminService->reject($listing, $request->reason);

        return back()->with('success', "Listing \"{$listing->title}\" rejected.");
    }

    public function setFeatured(Request $request, Listing $listing): RedirectResponse
    {
        $request->validate([
            'is_featured' => ['required', 'boolean'],
            'featured_until' => ['nullable', 'date', 'after:now'],
        ]);

        $this->adminService->setFeatured(
            $listing,
            (bool) $request->is_featured,
            $request->featured_until
        );

        $label = $request->is_featured ? 'featured' : 'unfeatured';

        return back()->with('success', "Listing \"{$listing->title}\" {$label}.");
    }
}
