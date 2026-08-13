<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SearchService
{
    private const PER_PAGE = 20;

    public function search(Request $request): LengthAwarePaginator
    {
        $query = Listing::visible()
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->with(['user', 'category', 'subcategory', 'images']);

        $this->applyKeyword($query, $request->input('q'));
        $this->applyCategory($query, $request->input('category'));
        $this->applySubcategory($query, $request->input('subcategory'));
        $this->applyCity($query, $request->input('city'));
        $this->applyState($query, $request->input('state'));
        $this->applyCountry($query, $request->input('country'));
        $this->applyPriceRange($query, $request->input('price_min'), $request->input('price_max'));
        $this->applyListingType($query, $request->input('listing_type'));
        $this->applySort($query, $request->input('sort', 'newest'));

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    public function autocomplete(string $term): array
    {
        if (strlen($term) < 2) {
            return [];
        }

        return Listing::visible()
            ->where('title', 'like', '%'.$term.'%')
            ->orderByDesc('view_count')
            ->limit(10)
            ->pluck('title', 'slug')
            ->map(fn ($title, $slug) => ['title' => $title, 'slug' => $slug])
            ->values()
            ->toArray();
    }

    private function applyKeyword($query, ?string $keyword): void
    {
        if (! $keyword) {
            return;
        }

        $query->where(function ($q) use ($keyword) {
            // Use FULLTEXT for terms >= 3 chars, LIKE fallback for shorter terms
            if (strlen($keyword) >= 3) {
                $q->whereRaw(
                    'MATCH(title, description) AGAINST(? IN BOOLEAN MODE)',
                    ['+'.implode('* +', explode(' ', trim($keyword))).'*']
                );
            } else {
                $q->where('title', 'like', '%'.$keyword.'%');
            }

            $q->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%'.$keyword.'%'))
                ->orWhereHas('subcategory', fn ($s) => $s->where('name', 'like', '%'.$keyword.'%'));
        });
    }

    private function applyCategory($query, ?string $categorySlug): void
    {
        if (! $categorySlug) {
            return;
        }

        $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
    }

    private function applySubcategory($query, ?string $subcategorySlug): void
    {
        if (! $subcategorySlug) {
            return;
        }

        $query->whereHas('subcategory', fn ($q) => $q->where('slug', $subcategorySlug));
    }

    private function applyCity($query, ?string $city): void
    {
        if (! $city) {
            return;
        }

        $query->where('location_city', 'like', '%'.$city.'%');
    }

    private function applyState($query, ?string $state): void
    {
        if (! $state) {
            return;
        }

        $query->where('location_state', 'like', '%'.$state.'%');
    }

    private function applyCountry($query, ?string $country): void
    {
        if (! $country) {
            return;
        }

        $query->where('location_country', 'like', '%'.$country.'%');
    }

    private function applyPriceRange($query, ?string $min, ?string $max): void
    {
        if ($min !== null && $min !== '') {
            $query->where('price', '>=', (float) $min);
        }

        if ($max !== null && $max !== '') {
            $query->where('price', '<=', (float) $max);
        }
    }

    private function applyListingType($query, ?string $type): void
    {
        if (! $type || ! in_array($type, ['buy', 'sell', 'rent'], true)) {
            return;
        }

        $query->where('listing_type', $type);
    }

    private function applySort($query, string $sort): void
    {
        // Featured listings always surface first
        $query->orderByDesc('is_featured');

        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'oldest' => $query->orderBy('created_at'),
            default => $query->orderByDesc('created_at'),
        };
    }
}
