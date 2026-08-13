<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Listing::visible()
            ->featured()
            ->with(['images', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $recent = Listing::visible()
            ->with(['images', 'category'])
            ->latest()
            ->take(16)
            ->get();

        $categories = Category::active()
            ->ordered()
            ->withCount(['listings' => fn ($q) => $q->visible()])
            ->get();

        return view('home', compact('featured', 'recent', 'categories'));
    }
}
