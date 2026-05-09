<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * AdminSeeder
 *
 * Creates the initial administrator account.
 *
 * Run: php artisan db:seed --class=AdminSeeder
 *
 * After running, log in with:
 *   Username : adm.admin
 *   Password : Admin@1234   (you will be forced to change this on first login)
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Prevent creating duplicates on re-seed
        if (User::where('username', 'adm.admin')->exists()) {
            $this->command->info('Admin account already exists. Skipping.');
            return;
        }

        User::create([
            'first_name'             => 'System',          // AES-256 encrypted by mutator
            'last_name'              => 'Administrator',   // AES-256 encrypted by mutator
            'email'                  => 'admin@encrypted.edu.ph', // AES-256 encrypted + SHA-256 hash
            'username'               => 'adm.admin',
            'password'               => 'Admin@1234',      // bcrypt hashed by mutator
            'role_id'                => '04',              // Admin
            'employee_number'        => 'EMP-0001',
            'password_reset_required'=> true,              // forced reset on first login
            'status'                 => 'active',
        ]);

        $this->command->info('✓ Admin account created.');
        $this->command->line('  Username : adm.admin');
        $this->command->line('  Password : Admin@1234  (change on first login)');
    }
}
