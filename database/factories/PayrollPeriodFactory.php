<?php

namespace Database\Factories;

use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollPeriod>
 */
class PayrollPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'month' => fake()->numberBetween(1, 12),
            'year' => (int) fake()->year(),
            'status' => fake()->randomElement(['draft', 'draft', 'processing', 'completed']),
            'processed_by' => null,
            'processed_at' => null,
        ];
    }

    /**
     * Mark payroll period as draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Mark payroll period as processing.
     */
    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    /**
     * Mark payroll period as completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'processed_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
