<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ListingApprovedNotification;
use App\Notifications\ListingRejectedNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function __construct(private readonly NotificationService $notificationService) {}

    public function approve(Listing $listing): Listing
    {
        DB::transaction(function () use ($listing) {
            $listing->update([
                'approval_status' => 'approved',
                'status' => 'active',
            ]);

            Cache::forget("listing:{$listing->slug}");
        });

        $fresh = $listing->fresh();
        $this->notificationService->send($fresh->user, new ListingApprovedNotification($fresh));

        return $fresh;
    }

    public function reject(Listing $listing, string $reason): Listing
    {
        DB::transaction(function () use ($listing) {
            $listing->update([
                'approval_status' => 'rejected',
                'status' => 'inactive',
            ]);

            Cache::forget("listing:{$listing->slug}");
        });

        $fresh = $listing->fresh();
        $this->notificationService->send($fresh->user, new ListingRejectedNotification($fresh, $reason));

        return $fresh;
    }

    public function setFeatured(Listing $listing, bool $featured, ?string $featuredUntil = null): Listing
    {
        $listing->update([
            'is_featured' => $featured,
            'featured_until' => $featured ? $featuredUntil : null,
        ]);

        Cache::forget("listing:{$listing->slug}");

        return $listing->fresh();
    }

    public function storeReport(User $reporter, Listing $listing, string $reason): ?Report
    {
        if (Report::where('listing_id', $listing->id)->where('reporter_id', $reporter->id)->exists()) {
            return null;
        }

        return Report::create([
            'listing_id' => $listing->id,
            'reporter_id' => $reporter->id,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    public function deactivateUser(User $user): User
    {
        $user->update(['is_active' => false]);
        $user->tokens()->delete();

        return $user->fresh();
    }

    public function activateUser(User $user): User
    {
        $user->update(['is_active' => true]);

        return $user->fresh();
    }
}
