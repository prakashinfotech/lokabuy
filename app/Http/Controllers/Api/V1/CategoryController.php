<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::rememberForever('categories.all', function () {
            return Category::active()
                ->ordered()
                ->withCount(['listings' => fn ($q) => $q->active()])
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'error' => null,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['subcategories' => fn ($q) => $q->active()->ordered()
            ->withCount(['listings' => fn ($q) => $q->active()])]);

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
            'error' => null,
        ]);
    }
}
