<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ReportPolicy
{
    public function create(User $user, Listing $listing): bool
    {
        return $user->id !== $listing->user_id;
    }
}
