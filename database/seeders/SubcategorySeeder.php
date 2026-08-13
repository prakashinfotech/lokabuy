<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Vehicles' => [
                'Cars', 'Motorcycles', 'Scooters', 'Commercial Vehicles',
                'Trucks', 'Buses', 'Bicycles', 'Spare Parts & Accessories',
            ],
            'Real Estate' => [
                'Flats / Apartments', 'Houses & Villas', 'Plots & Land',
                'PG / Co-living', 'Commercial / Office Space', 'Shops & Showrooms',
                'Warehouse / Godown',
            ],
            'Electronics' => [
                'Mobile Phones', 'Tablets', 'Laptops & Computers', 'Cameras & Lenses',
                'TVs & Monitors', 'Refrigerators', 'Washing Machines',
                'Air Conditioners', 'Audio & Headphones', 'Gaming',
            ],
            'Furniture & Home' => [
                'Sofas & Dining', 'Beds & Wardrobes', 'Home Décor',
                'Kitchen Appliances', 'Fans & Heaters', 'Garden & Outdoor',
            ],
            'Fashion' => [
                "Men's Clothing", "Women's Clothing", "Kids' Clothing",
                'Watches & Accessories', 'Footwear', 'Jewellery',
            ],
            'Books & Sports' => [
                'Books', 'Sports Equipment', 'Gym & Fitness',
                'Musical Instruments', 'Board Games & Toys',
            ],
            'Jobs' => [
                'Data Entry & Back Office', 'Sales & Marketing', 'BPO & Telecaller',
                'Driver', 'Office Assistant', 'Delivery & Logistics',
                'Teacher', 'Cook / Chef', 'Receptionist / Front Office',
                'Other Jobs',
            ],
            'Services' => [
                'Packers & Movers', 'Home Cleaning', 'Pest Control',
                'Plumbing', 'Electrician', 'Carpentry', 'Beauty & Spa',
                'Catering', 'Photography', 'Other Services',
            ],
            'Pets' => [
                'Dogs', 'Cats', 'Birds', 'Fish & Aquarium',
                'Pet Food & Accessories', 'Other Pets',
            ],
            'Agriculture' => [
                'Crops & Seeds', 'Fertilisers & Pesticides', 'Farm Machinery',
                'Irrigation Equipment', 'Livestock', 'Other Agriculture',
            ],
        ];

        foreach ($map as $categoryName => $subcategoryNames) {
            $category = Category::where('slug', Str::slug($categoryName))->first();

            if (! $category) {
                continue;
            }

            foreach ($subcategoryNames as $index => $name) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::slug($categoryName),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
