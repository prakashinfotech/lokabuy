<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingRequest;
use App\Http\Requests\Listing\UpdateListingRequest;
use App\Models\Category;
use App\Models\Country;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Services\ImageService;
use App\Services\ListingService;
use App\Services\SearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function __construct(
        private readonly ListingService $listingService,
        private readonly SearchService $searchService,
        private readonly ImageService $imageService,
    ) {}

    public function index(Request $request): View
    {
        $listings = $this->searchService->search($request);

        $categories = Cache::rememberForever('categories.all.web', function () {
            return Category::active()->ordered()->get();
        });

        $subcategories = collect();
        if ($categorySlug = $request->input('category')) {
            $selectedCategory = $categories->firstWhere('slug', $categorySlug);
            if ($selectedCategory) {
                $subcategories = $selectedCategory->subcategories()->active()->ordered()->get();
            }
        }

        $countries = Cache::rememberForever('countries.all', fn () => Country::active()->ordered()->pluck('name'));

        return view('listings.index', compact('listings', 'categories', 'subcategories', 'countries'));
    }

    public function show(Listing $listing): View
    {
        // Only visible (active + approved) listings are publicly accessible
        abort_unless($listing->status === 'active' && $listing->approval_status === 'approved', 404);

        $listing->load(['user', 'category', 'subcategory', 'images']);

        $this->listingService->incrementViewCount($listing);

        // Up to 6 related listings from the same subcategory, falling back to category
        $relatedQuery = Listing::visible()
            ->where('id', '!=', $listing->id)
            ->with(['images'])
            ->limit(6);

        if ($listing->subcategory_id) {
            $related = (clone $relatedQuery)
                ->where('subcategory_id', $listing->subcategory_id)
                ->latest()->get();
        } else {
            $related = collect();
        }

        if ($related->count() < 6) {
            $exclude = $related->pluck('id')->push($listing->id);
            $extra = (clone $relatedQuery)
                ->whereNotIn('id', $exclude)
                ->where('category_id', $listing->category_id)
                ->limit(6 - $related->count())
                ->latest()->get();
            $related = $related->merge($extra);
        }

        return view('listings.show', compact('listing', 'related'));
    }

    public function create(): View
    {
        $categories = Cache::rememberForever('categories.all.web', function () {
            return Category::active()->ordered()->with(['subcategories' => fn ($q) => $q->active()->ordered()])->get();
        });

        $countries = Cache::rememberForever('countries.all', fn () => Country::active()->ordered()->pluck('name'));

        return view('listings.create', compact('categories', 'countries'));
    }

    public function store(StoreListingRequest $request): RedirectResponse
    {
        $this->listingService->createWithImages(
            $request->user(),
            $request->validated(),
            $request->file('images', [])
        );

        return redirect()->route('listings.my')
            ->with('success', 'Ad posted successfully! It will be visible after admin approval.');
    }

    public function edit(Listing $listing): View
    {
        $this->authorize('update', $listing);

        $categories = Cache::rememberForever('categories.all.web', function () {
            return Category::active()->ordered()->with(['subcategories' => fn ($q) => $q->active()->ordered()])->get();
        });

        $countries = Cache::rememberForever('countries.all', fn () => Country::active()->ordered()->pluck('name'));

        $listing->load(['category', 'subcategory', 'images']);

        return view('listings.edit', compact('listing', 'categories', 'countries'));
    }

    public function update(UpdateListingRequest $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $this->listingService->updateWithImages(
            $listing,
            $request->validated(),
            $request->file('images', [])
        );

        return redirect()->route('listings.my')
            ->with('success', 'Listing updated successfully.');
    }

    public function destroyImage(Listing $listing, ListingImage $image): RedirectResponse
    {
        $this->authorize('deleteImage', $listing);

        abort_if($image->listing_id !== $listing->id, 404);

        $this->imageService->delete($image);

        return back()->with('success', 'Image removed.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        $this->listingService->delete($listing);

        return redirect()->route('listings.my')
            ->with('success', 'Listing deleted.');
    }

    public function myListings(): View
    {
        $listings = Listing::where('user_id', auth()->id())
            ->with(['category', 'images'])
            ->latest()
            ->paginate(15);

        return view('listings.my-listings', compact('listings'));
    }

    public function markAsSold(Listing $listing): RedirectResponse
    {
        $this->authorize('markAsSold', $listing);

        $this->listingService->markAsSold($listing);

        return back()->with('success', 'Listing marked as sold.');
    }

    public function renew(Listing $listing): RedirectResponse
    {
        $this->authorize('renew', $listing);

        $this->listingService->renew($listing);

        return back()->with('success', 'Listing renewed for 60 days.');
    }
}
