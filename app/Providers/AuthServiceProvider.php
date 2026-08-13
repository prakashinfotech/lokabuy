<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Listing;
use App\Models\Report;
use App\Policies\ListingPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Listing::class => ListingPolicy::class,
        Report::class => ReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
