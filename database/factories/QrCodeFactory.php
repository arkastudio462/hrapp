<?php

namespace Database\Factories;

use App\Models\QrCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QrCode>
 */
class QrCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Str::random(32),
            'date' => fake()->dateTimeThisMonth(),
            'expires_at' => fake()->dateTimeBetween('+1 hours', '+8 hours'),
            'is_active' => true,
            'created_by' => null,
        ];
    }

    /**
     * Mark the QR code as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => fake()->dateTimeBetween('-8 hours', '-1 hours'),
        ]);
    }

    /**
     * Mark the QR code as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
