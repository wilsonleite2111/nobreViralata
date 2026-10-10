<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'confirmed_at' => now(),
            'unsubscribed_at' => null,
        ];
    }

    public function unconfirmed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'confirmed_at' => null,
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'unsubscribed_at' => now(),
        ]);
    }
}
