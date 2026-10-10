<?php

use App\Filament\Resources\ProductCategories\Pages\ManageProductCategories;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the categories admin', function (): void {
    auth()->logout();

    $this->get('/admin/product-categories')->assertRedirect('/admin/login');
});

it('lists categories in the admin', function (): void {
    $categories = ProductCategory::factory()->count(2)->create();

    Livewire::test(ManageProductCategories::class)->assertCanSeeTableRecords($categories);
});

it('creates a category', function (): void {
    Livewire::test(ManageProductCategories::class)
        ->callAction('create', data: ['name' => 'Camisetas', 'slug' => 'camisetas'])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('product_categories', ['name' => 'Camisetas', 'slug' => 'camisetas']);
});

it('rejects a slug already used by another category', function (): void {
    ProductCategory::factory()->create(['slug' => 'taken']);

    Livewire::test(ManageProductCategories::class)
        ->callAction('create', data: ['name' => 'Other', 'slug' => 'taken'])
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('keeps the products of a deleted category, now uncategorized', function (): void {
    $category = ProductCategory::factory()->create();
    $product = Product::factory()->for($category, 'category')->create();

    Livewire::test(ManageProductCategories::class)->callTableAction('delete', $category);

    $this->assertModelMissing($category);
    expect($product->refresh()->product_category_id)->toBeNull();
});
