<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'head_id' => null,
            'parent_id' => null,
            'budget' => fake()->randomFloat(2, 100000, 10000000),
        ];
    }

    /**
     * Mark the department as having no budget.
     */
    public function noBudget(): static
    {
        return $this->state(fn (array $attributes) => [
            'budget' => null,
        ]);
    }
}
