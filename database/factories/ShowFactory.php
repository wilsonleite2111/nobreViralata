<?php

namespace Database\Factories;

use App\Models\Show;
use App\ShowStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Show>
 */
class ShowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'starts_at' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'venue' => fake()->company(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'address' => fake()->streetAddress(),
            'ticket_url' => fake()->url(),
            'status' => ShowStatus::Scheduled,
            'description' => fake()->paragraph(),
            'is_published' => true,
        ];
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes): array => [
            'starts_at' => fake()->dateTimeBetween('-1 year', '-1 week'),
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShowStatus::SoldOut,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ShowStatus::Cancelled,
        ]);
    }
}
