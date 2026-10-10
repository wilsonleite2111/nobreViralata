<?php

namespace Database\Factories;

use App\Models\Video;
use App\VideoCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'youtube_id' => fake()->unique()->regexify('[A-Za-z0-9_-]{11}'),
            'category' => VideoCategory::Live,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'show_id' => null,
            'is_featured' => false,
            'is_published' => true,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }
}
