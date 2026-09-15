<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'package_id' => SubscriptionPackage::factory(),
            'invoice_number' => 'INV-'.fake()->unique()->numerify('########'),
            'amount' => fake()->randomFloat(2, 100000, 5000000),
            'status' => fake()->randomElement(['pending', 'pending', 'paid', 'overdue']),
            'description' => fake()->sentence(),
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'paid_at' => null,
            'payment_method' => null,
        ];
    }

    /**
     * Mark invoice as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Mark invoice as paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'payment_method' => fake()->randomElement(['bank_transfer', 'credit_card', 'e_wallet', 'cash']),
        ]);
    }

    /**
     * Mark invoice as overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
            'due_date' => fake()->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }
}
