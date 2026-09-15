<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = fake()->numberBetween(5, 20);
        $used = fake()->numberBetween(0, $total);

        return [
            'employee_id' => Employee::factory(),
            'year' => (int) fake()->year(),
            'type' => fake()->randomElement(['annual', 'sick', 'maternity']),
            'total' => $total,
            'used' => $used,
            'remaining' => $total - $used,
        ];
    }

    /**
     * Set leave balance type to annual.
     */
    public function annual(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'annual',
        ]);
    }

    /**
     * Set leave balance type to sick.
     */
    public function sick(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sick',
        ]);
    }

    /**
     * Set leave balance type to maternity.
     */
    public function maternity(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'maternity',
        ]);
    }

    /**
     * Mark all leave as used.
     */
    public function fullyUsed(): static
    {
        return $this->state(fn (array $attributes) => [
            'used' => $attributes['total'],
            'remaining' => 0,
        ]);
    }
}
