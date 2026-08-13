<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@lokabuy.com')],
            [
                'name' => 'Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'secret')),
                'role' => 'admin',
                'is_verified' => true,
                'is_active' => true,
                'email_notifications' => true,
            ]
        );
    }
}
