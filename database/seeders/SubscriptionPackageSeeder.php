<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Free Trial',
                'slug' => 'free-trial',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_employees' => 10,
                'max_storage_gb' => 1,
                'features' => [
                    'attendance' => true,
                    'payroll' => true,
                    'face_detection' => false,
                    'reports' => false,
                    'api_access' => false,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price_monthly' => 199000,
                'price_yearly' => 1990000,
                'max_employees' => 25,
                'max_storage_gb' => 5,
                'features' => [
                    'attendance' => true,
                    'payroll' => true,
                    'face_detection' => false,
                    'reports' => true,
                    'api_access' => false,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'price_monthly' => 499000,
                'price_yearly' => 4990000,
                'max_employees' => 100,
                'max_storage_gb' => 20,
                'features' => [
                    'attendance' => true,
                    'payroll' => true,
                    'face_detection' => true,
                    'reports' => true,
                    'api_access' => false,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price_monthly' => 999000,
                'price_yearly' => 9990000,
                'max_employees' => 999999,
                'max_storage_gb' => 100,
                'features' => [
                    'attendance' => true,
                    'payroll' => true,
                    'face_detection' => true,
                    'reports' => true,
                    'api_access' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
