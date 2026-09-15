<?php

namespace Database\Factories;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubscriptionPackage>
 */
class SubscriptionPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Basic', 'Standard', 'Premium', 'Enterprise']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'price_monthly' => fake()->randomFloat(2, 0, 5000000),
            'price_yearly' => fake()->randomFloat(2, 0, 50000000),
            'max_employees' => fake()->randomElement([10, 25, 50, 100, 250, 500]),
            'max_storage_gb' => fake()->randomElement([1, 5, 10, 25, 50]),
            'features' => fake()->randomElements([
                'attendance',
                'payroll',
                'leave_management',
                'reporting',
                'api_access',
                'custom_branding',
                'priority_support',
            ], fake()->numberBetween(2, 5)),
            'is_active' => true,
        ];
    }

    /**
     * Mark package as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a free trial package.
     */
    public function freeTrial(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Free Trial',
            'slug' => 'free-trial',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'max_employees' => 5,
            'max_storage_gb' => 1,
            'features' => ['attendance', 'leave_management'],
        ]);
    }
}
