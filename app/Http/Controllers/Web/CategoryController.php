<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Cache::rememberForever('categories.all.web', function () {
            return Category::active()
                ->ordered()
                ->withCount(['listings' => fn ($q) => $q->active()])
                ->get();
        });

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $category->load(['subcategories' => fn ($q) => $q->active()->ordered()
            ->withCount(['listings' => fn ($q) => $q->active()])]);

        return view('categories.show', compact('category'));
    }
}
