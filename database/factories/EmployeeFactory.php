<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => 'EMP'.fake()->numerify('#####'),
            'name' => fake()->name(),
            'email_personal' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-50 years', '-20 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->address(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'join_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'status' => fake()->randomElement(['contract', 'permanent', 'probation']),
            'is_active' => true,
        ];
    }

    /**
     * Mark the employee as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set employee status to contract.
     */
    public function contract(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'contract',
        ]);
    }

    /**
     * Set employee status to permanent.
     */
    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'permanent',
        ]);
    }

    /**
     * Set employee status to probation.
     */
    public function probation(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'probation',
        ]);
    }
}
