<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basicSalary = fake()->randomFloat(2, 3000000, 20000000);
        $totalEarnings = fake()->randomFloat(2, 500000, 5000000);
        $totalDeductions = fake()->randomFloat(2, 200000, 2000000);

        return [
            'employee_id' => Employee::factory(),
            'payroll_period_id' => PayrollPeriod::factory(),
            'basic_salary' => $basicSalary,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $basicSalary + $totalEarnings - $totalDeductions,
            'status' => fake()->randomElement(['draft', 'paid']),
        ];
    }

    /**
     * Mark payroll as draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Mark payroll as paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }
}
