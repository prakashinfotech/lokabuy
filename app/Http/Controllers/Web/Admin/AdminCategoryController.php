<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\StoreSubcategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Requests\Admin\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['listings' => fn ($q) => $q->active()])
            ->with(['subcategories' => fn ($q) => $q->ordered()])
            ->ordered()
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $iconUrl = null;
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $iconUrl = Storage::disk('public')->url($path);
        }

        Category::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'icon' => $iconUrl,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        $this->bustCache();

        return back()->with('success', 'Category created.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->except('icon');

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('icons', 'public');
            $data['icon'] = Storage::disk('public')->url($path);
        }

        $category->update($data);

        $this->bustCache();

        return back()->with('success', 'Category updated.');
    }

    public function storeSubcategory(StoreSubcategoryRequest $request, Category $category): RedirectResponse
    {
        $baseSlug = Str::slug($category->slug.'-'.$request->name);
        $slug = $baseSlug;
        $i = 1;
        while (Subcategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $category->subcategories()->create([
            'name' => $request->name,
            'slug' => $slug,
            'sort_order' => (int) ($category->subcategories()->max('sort_order') ?? -1) + 1,
            'is_active' => true,
        ]);

        $this->bustCache();

        return back()->with('success', 'Subcategory added.');
    }

    public function updateSubcategory(UpdateSubcategoryRequest $request, Category $category, Subcategory $subcategory): RedirectResponse
    {
        abort_if($subcategory->category_id !== $category->id, 404);

        $subcategory->update(['name' => $request->name]);

        $this->bustCache();

        return back()->with('success', 'Subcategory updated.');
    }

    public function toggleSubcategory(Category $category, Subcategory $subcategory): RedirectResponse
    {
        abort_if($subcategory->category_id !== $category->id, 404);

        $newState = ! $subcategory->is_active;
        $subcategory->update(['is_active' => $newState]);

        $this->bustCache();

        return back()->with('success', 'Subcategory '.($newState ? 'activated' : 'deactivated').'.');
    }

    private function bustCache(): void
    {
        Cache::forget('categories.all');
        Cache::forget('categories.all.web');
    }
}
