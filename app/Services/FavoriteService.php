<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Favorite;
use App\Models\Listing;
use App\Models\User;

class FavoriteService
{
    public function toggle(User $user, Listing $listing): bool
    {
        $existing = Favorite::where('user_id', $user->id)
            ->where('listing_id', $listing->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Favorite::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);

        return true;
    }
}
