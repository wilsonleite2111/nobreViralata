<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed sample products for local development: shirts with sizes and single-option accessories.
     */
    public function run(): void
    {
        $this->call(ProductCategorySeeder::class);

        $shirts = ProductCategory::query()->where('slug', 'camisetas')->sole();
        $accessories = ProductCategory::query()->where('slug', 'acessorios')->sole();

        Product::factory()->count(2)->for($shirts, 'category')->create()->each(function (Product $product): void {
            foreach (['P', 'M', 'G', 'GG'] as $position => $size) {
                ProductVariant::factory()->for($product)->create([
                    'name' => $size,
                    'sort_order' => $position + 1,
                    'stock' => $size === 'GG' ? 0 : 5,
                ]);
            }
        });

        Product::factory()->count(2)->for($accessories, 'category')->create();
        Product::factory()->outOfStock()->for($accessories, 'category')->create();

        Product::query()->each(function (Product $product): void {
            $product->addMedia(public_path('images/logo.jpg'))
                ->preservingOriginal()
                ->toMediaCollection(Product::PHOTOS);
        });
    }
}
