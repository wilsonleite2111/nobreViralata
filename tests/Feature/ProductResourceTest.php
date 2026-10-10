<?php

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the products admin', function (): void {
    auth()->logout();

    $this->get('/admin/products')->assertRedirect('/admin/login');
});

it('lists products in the admin', function (): void {
    $products = Product::factory()->count(3)->create();

    Livewire::test(ListProducts::class)->assertCanSeeTableRecords($products);
});

it('creates a product with photo and variants that then appears in the store', function (): void {
    Storage::fake('public');
    $undoRepeaterFake = Repeater::fake();
    $category = ProductCategory::factory()->create();

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'name' => 'Camiseta Vira Lata',
            'slug' => 'camiseta-vira-lata',
            'product_category_id' => $category->id,
            'price_cents' => '89.90',
            'photos' => [UploadedFile::fake()->image('shirt.jpg', 1200, 1200)],
            'variants' => [
                ['name' => 'M', 'stock' => 3],
                ['name' => 'G', 'stock' => 0],
            ],
            'stock' => 0,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    $product = Product::query()->where('slug', 'camiseta-vira-lata')->sole();

    expect($product->price_cents)->toBe(8990)
        ->and($product->category->is($category))->toBeTrue()
        ->and($product->variants->pluck('stock', 'name')->all())->toBe(['M' => 3, 'G' => 0])
        ->and($product->isInStock())->toBeTrue();

    Storage::disk('public')->assertExists($product->cover()->getPathRelativeToRoot('thumb'));
    $this->get('/loja')->assertSee('Camiseta Vira Lata')->assertSee('R$ 89,90');
});

it('shows the stored price in reais when editing and keeps it on save', function (): void {
    $product = Product::factory()->create(['price_cents' => 12050]);

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->assertSchemaStateSet(['price_cents' => '120.50'])
        ->fillForm(['name' => 'Changed Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh())
        ->name->toBe('Changed Name')
        ->price_cents->toBe(12050);
});

it('removes a variant deleted in the form', function (): void {
    $undoRepeaterFake = Repeater::fake();
    $product = Product::factory()->create();
    $kept = ProductVariant::factory()->for($product)->create(['name' => 'M', 'sort_order' => 1]);
    ProductVariant::factory()->for($product)->create(['name' => 'G', 'sort_order' => 2]);

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->fillForm(['variants' => [['name' => 'M', 'stock' => 7]]])
        ->call('save')
        ->assertHasNoFormErrors();

    $undoRepeaterFake();

    expect($product->variants()->pluck('name')->all())->toBe(['M'])
        ->and($product->variants()->sole()->stock)->toBe(7);
});

it('rejects a product without its required fields', function (): void {
    Livewire::test(CreateProduct::class)
        ->fillForm(['name' => null, 'slug' => null, 'price_cents' => null])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'slug' => 'required',
            'price_cents' => 'required',
        ]);
});

it('rejects an invalid price or stock', function (array $data, string $field): void {
    Livewire::test(CreateProduct::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasFormErrors([$field]);
})->with([
    'zero price' => [['price_cents' => '0'], 'price_cents'],
    'negative price' => [['price_cents' => '-5'], 'price_cents'],
    'text price' => [['price_cents' => 'caro'], 'price_cents'],
    'negative stock' => [['stock' => -1], 'stock'],
]);

it('rejects an external link that is not a web address', function (): void {
    Livewire::test(CreateProduct::class)
        ->fillForm(['external_url' => 'javascript://alert(1)'])
        ->call('create')
        ->assertHasFormErrors(['external_url']);
});

it('rejects a slug already used by another product', function (): void {
    Product::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateProduct::class)
        ->fillForm(['slug' => 'taken'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});
