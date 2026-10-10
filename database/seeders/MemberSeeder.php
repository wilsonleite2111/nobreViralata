<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Seed sample band members and history for local development.
     */
    public function run(): void
    {
        foreach (['Vocal', 'Guitarra', 'Baixo', 'Bateria', 'Teclado'] as $position => $role) {
            Member::factory()->create(['role' => $role, 'sort_order' => $position + 1]);
        }

        Setting::write(Setting::BAND_HISTORY, '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>');
    }
}
