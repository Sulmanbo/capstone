<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Skip if a test user already exists (idempotent for repeated seeds)
        if (User::where('username', 'testuser')->exists()) {
            return;
        }

        User::factory()->create([
            'first_name' => 'Test',
            'last_name'  => 'User',
            'username'   => 'testuser',
            'email'      => 'test@example.com',
            'role_id'    => '04',          // admin so manual testing has full access
        ]);
    }
}
