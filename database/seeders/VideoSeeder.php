<?php

namespace Database\Seeders;

use App\Models\Video;
use App\VideoCategory;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Seed sample videos for local development.
     */
    public function run(): void
    {
        foreach (VideoCategory::cases() as $category) {
            Video::factory()->count(2)->create(['category' => $category]);
        }
    }
}
