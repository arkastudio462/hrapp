<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(0, 5).' days');

        return [
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(['leave', 'sick', 'permission']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'reason' => fake()->sentence(),
            'attachment' => null,
            'status' => fake()->randomElement(['pending', 'pending', 'approved', 'rejected']),
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    /**
     * Mark leave request as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Mark leave request as approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Mark leave request as rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
