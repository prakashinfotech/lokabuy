<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Vehicles',          'icon' => 'bi-car-front',       'sort_order' => 1],
            ['name' => 'Real Estate',        'icon' => 'bi-house',           'sort_order' => 2],
            ['name' => 'Electronics',        'icon' => 'bi-laptop',          'sort_order' => 3],
            ['name' => 'Furniture & Home',   'icon' => 'bi-lamp',            'sort_order' => 4],
            ['name' => 'Fashion',            'icon' => 'bi-bag',             'sort_order' => 5],
            ['name' => 'Books & Sports',     'icon' => 'bi-book',            'sort_order' => 6],
            ['name' => 'Jobs',               'icon' => 'bi-briefcase',       'sort_order' => 7],
            ['name' => 'Services',           'icon' => 'bi-tools',           'sort_order' => 8],
            ['name' => 'Pets',               'icon' => 'bi-github',          'sort_order' => 9],
            ['name' => 'Agriculture',        'icon' => 'bi-flower1',         'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}
