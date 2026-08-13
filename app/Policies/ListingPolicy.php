<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function markAsSold(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function renew(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function uploadImage(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function deleteImage(User $user, Listing $listing): bool
    {
        return $user->id === $listing->user_id;
    }
}
