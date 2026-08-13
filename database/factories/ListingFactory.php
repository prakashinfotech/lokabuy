<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ListingFactory extends Factory
{
    protected $model = Listing::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(4, true);
        $base = Str::slug($title);
        $slug = $base.'-'.Str::lower(Str::random(6));

        return [
            'user_id' => User::factory(),
            'category_id' => Category::inRandomOrder()->value('id'),
            'subcategory_id' => null,
            'title' => rtrim($title, '.'),
            'slug' => $slug,
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 100, 500000),
            'currency' => 'INR',
            'listing_type' => null,
            'location_city' => $this->faker->city,
            'location_state' => $this->faker->state,
            'status' => 'active',
            'approval_status' => 'approved',
            'is_featured' => false,
            'view_count' => $this->faker->numberBetween(0, 500),
            'expires_at' => now()->addDays(60),
        ];
    }
}
