<?php

namespace Database\Seeders;

use App\Models\PhotoAlbum;
use Illuminate\Database\Seeder;

class PhotoAlbumSeeder extends Seeder
{
    /**
     * Seed sample albums for local development, each with the official band photo.
     */
    public function run(): void
    {
        PhotoAlbum::factory()->count(3)->create()->each(function (PhotoAlbum $album): void {
            foreach (range(1, 4) as $ignored) {
                $album->addMedia(public_path('images/banda.jpg'))
                    ->preservingOriginal()
                    ->toMediaCollection(PhotoAlbum::PHOTOS);
            }
        });
    }
}
