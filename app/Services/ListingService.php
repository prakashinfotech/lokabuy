<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListingService
{
    public function __construct(private readonly ImageService $imageService) {}

    public function createWithImages(User $user, array $data, array $files): Listing
    {
        return DB::transaction(function () use ($user, $data, $files) {
            $listing = $this->create($user, $data);

            foreach ($files as $file) {
                if (! $this->imageService->canAddImages($listing)) {
                    break;
                }
                $this->imageService->store($listing, $file);
            }

            return $listing;
        });
    }

    public function updateWithImages(Listing $listing, array $data, array $files): Listing
    {
        return DB::transaction(function () use ($listing, $data, $files) {
            $updated = $this->update($listing, $data);

            foreach ($files as $file) {
                if (! $this->imageService->canAddImages($updated)) {
                    break;
                }
                $this->imageService->store($updated, $file);
            }

            return $updated;
        });
    }

    public function create(User $user, array $data): Listing
    {
        return Listing::create([
            'user_id' => $user->id,
            'category_id' => $data['category_id'],
            'subcategory_id' => $data['subcategory_id'] ?? null,
            'title' => $data['title'],
            'slug' => $this->generateSlug($data['title']),
            'description' => $data['description'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'INR',
            'listing_type' => $data['listing_type'] ?? null,
            'location_city' => $data['location_city'],
            'location_state' => $data['location_state'],
            'status' => 'inactive',
            'approval_status' => 'pending',
            'expires_at' => now()->addDays(60),
        ]);
    }

    public function update(Listing $listing, array $data): Listing
    {
        $listing->update([
            'category_id' => $data['category_id'] ?? $listing->category_id,
            'subcategory_id' => array_key_exists('subcategory_id', $data) ? $data['subcategory_id'] : $listing->subcategory_id,
            'title' => $data['title'] ?? $listing->title,
            'description' => $data['description'] ?? $listing->description,
            'price' => array_key_exists('price', $data) ? $data['price'] : $listing->getRawOriginal('price'),
            'listing_type' => $data['listing_type'] ?? $listing->listing_type,
            'location_city' => $data['location_city'] ?? $listing->location_city,
            'location_state' => $data['location_state'] ?? $listing->location_state,
        ]);

        Cache::forget("listing:{$listing->slug}");

        return $listing->fresh();
    }

    public function delete(Listing $listing): void
    {
        Cache::forget("listing:{$listing->slug}");
        $listing->delete();
    }

    public function markAsSold(Listing $listing): Listing
    {
        $listing->update(['status' => 'sold']);
        Cache::forget("listing:{$listing->slug}");

        return $listing->fresh();
    }

    public function renew(Listing $listing): Listing
    {
        $listing->update([
            'expires_at' => now()->addDays(60),
            'status' => 'active',
        ]);

        Cache::forget("listing:{$listing->slug}");

        return $listing->fresh();
    }

    public function incrementViewCount(Listing $listing): void
    {
        $listing->increment('view_count');
        Cache::forget("listing:{$listing->slug}");
    }

    public function getUserStats(User $user): array
    {
        return [
            'active_listings' => Listing::where('user_id', $user->id)->where('status', 'active')->count(),
            'sold_listings' => Listing::where('user_id', $user->id)->where('status', 'sold')->count(),
            'total_views' => (int) Listing::where('user_id', $user->id)->sum('view_count'),
            'pending_listings' => Listing::where('user_id', $user->id)->where('approval_status', 'pending')->count(),
        ];
    }

    public function getRecentListings(User $user, int $limit = 5): Collection
    {
        return Listing::where('user_id', $user->id)
            ->with(['category', 'images'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getUserListings(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Listing::where('user_id', $user->id)
            ->with(['category', 'images'])
            ->latest()
            ->paginate($perPage);
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $suffix = Str::lower(Str::random(6));
        $slug = "{$base}-{$suffix}";

        while (Listing::withTrashed()->where('slug', $slug)->exists()) {
            $suffix = Str::lower(Str::random(6));
            $slug = "{$base}-{$suffix}";
        }

        return $slug;
    }
}
