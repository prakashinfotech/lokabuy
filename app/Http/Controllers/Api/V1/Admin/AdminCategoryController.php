<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('subcategories')
            ->withCount(['listings' => fn ($q) => $q->active()])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'error' => null,
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->bustCache();

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'error' => null,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['name']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        $this->bustCache();

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category->fresh()),
            'error' => null,
        ]);
    }

    private function bustCache(): void
    {
        Cache::forget('categories.all');
        Cache::forget('categories.all.web');
    }
}
