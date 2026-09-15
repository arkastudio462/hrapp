<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInTime = fake()->dateTimeBetween('-12 hours', '-4 hours');
        $isLate = fake()->boolean(20);

        return [
            'employee_id' => Employee::factory(),
            'date' => fake()->dateTimeThisMonth(),
            'check_in_time' => $isLate ? $checkInTime->modify('+1 hour') : $checkInTime,
            'check_out_time' => fake()->optional(0.7)->dateTimeBetween($checkInTime, '+8 hours'),
            'check_in_photo' => fake()->optional(0.5)->filePath(),
            'check_in_location' => [
                'lat' => fake()->latitude(-6.2, -6.1),
                'lng' => fake()->longitude(106.8, 106.9),
            ],
            'check_in_method' => fake()->randomElement(['face', 'qr']),
            'qr_code_id' => null,
            'status' => $isLate ? 'late' : fake()->randomElement(['present', 'present', 'present', 'absent']),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Mark attendance as present.
     */
    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'present',
        ]);
    }

    /**
     * Mark attendance as late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'late',
        ]);
    }

    /**
     * Mark attendance as absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'absent',
            'check_in_time' => null,
            'check_out_time' => null,
        ]);
    }
}
