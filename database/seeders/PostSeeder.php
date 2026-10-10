<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Seed sample news for local development.
     */
    public function run(): void
    {
        Post::factory()->count(5)->create()->each(function (Post $post): void {
            $post->addMedia(public_path('images/banda.jpg'))
                ->preservingOriginal()
                ->toMediaCollection(Post::COVER);
        });
    }
}
