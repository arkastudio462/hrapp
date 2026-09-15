<?php

namespace Database\Factories;

use App\Models\PayrollComponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollComponent>
 */
class PayrollComponentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('??##'),
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['earning', 'deduction']),
            'calculation_type' => fake()->randomElement(['fixed', 'percentage', 'variable']),
            'default_value' => fake()->randomFloat(2, 0, 100),
            'is_active' => true,
        ];
    }

    /**
     * Mark component as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set calculation type to fixed.
     */
    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_type' => 'fixed',
        ]);
    }

    /**
     * Set calculation type to percentage.
     */
    public function percentage(): static
    {
        return $this->state(fn (array $attributes) => [
            'calculation_type' => 'percentage',
        ]);
    }
}
