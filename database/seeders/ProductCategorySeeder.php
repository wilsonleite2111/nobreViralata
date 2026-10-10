<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Seed sample store categories for local development.
     */
    public function run(): void
    {
        foreach (['Camisetas' => 'camisetas', 'Acessórios' => 'acessorios'] as $name => $slug) {
            ProductCategory::query()->firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
