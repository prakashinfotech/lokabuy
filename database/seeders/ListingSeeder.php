<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    private int $imageCounter = 1;

    public function run(): void
    {
        $users = User::where('role', 'user')->pluck('id');

        if ($users->isEmpty()) {
            User::factory(5)->create()->each(fn ($u) => $u->update([
                'is_active' => true,
                'is_verified' => true,
                'email_notifications' => true,
            ]));
            $users = User::where('role', 'user')->pluck('id');
        }

        Category::all()->each(function (Category $category) use ($users) {
            Listing::factory(1)->create([
                'category_id' => $category->id,
                'user_id' => $users->random(),
                'approval_status' => 'approved',
                'status' => 'active',
            ])->each(function (Listing $listing) {
                $imageCount = rand(1, 4);

                for ($i = 0; $i < $imageCount; $i++) {
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'url' => "https://picsum.photos/seed/{$this->imageCounter}/640/480",
                        'order' => $i,
                    ]);
                    $this->imageCounter++;
                }
            });
        });
    }
}
