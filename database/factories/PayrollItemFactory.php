<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollItem>
 */
class PayrollItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['earning', 'deduction']);

        return [
            'payroll_id' => Payroll::factory(),
            'component_code' => fake()->randomElement(['BASIC', 'HOUSING', 'TRANSPORT', 'TAX', 'BPJS']),
            'amount' => fake()->randomFloat(2, 100000, 5000000),
            'type' => $type,
            'description' => fake()->sentence(),
        ];
    }

    /**
     * Set item type to earning.
     */
    public function earning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'earning',
        ]);
    }

    /**
     * Set item type to deduction.
     */
    public function deduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'deduction',
        ]);
    }
}
