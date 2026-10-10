<?php

namespace Database\Seeders;

use App\Models\Show;
use Illuminate\Database\Seeder;

class ShowSeeder extends Seeder
{
    /**
     * Seed sample shows for local development.
     */
    public function run(): void
    {
        Show::factory()->count(4)->create();
        Show::factory()->soldOut()->create();
        Show::factory()->cancelled()->create();
        Show::factory()->past()->count(6)->create();
    }
}
