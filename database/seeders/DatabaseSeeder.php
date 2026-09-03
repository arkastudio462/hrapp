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
        $this->call([
            SubscriptionPackageSeeder::class,
        ]);

        // Create super admin user
        User::firstOrCreate(
            ['email' => 'admin@hrhub.id'],
            [
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create demo tenant with complete data
        $this->call([
            DemoTenantSeeder::class,
        ]);
    }
}
