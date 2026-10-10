<?php

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('store list', function (): void {
    it('lists active products with their price', function (): void {
        Product::factory()->create(['name' => 'Band Shirt', 'price_cents' => 8990]);
        Product::factory()->inactive()->create(['name' => 'Hidden Shirt']);

        $this->get('/loja')
            ->assertOk()
            ->assertSee('Band Shirt')
            ->assertSee('R$ 89,90')
            ->assertDontSee('Hidden Shirt');
    });

    it('flags a product without stock as sold out', function (): void {
        Product::factory()->create(['name' => 'Available Cap']);
        $this->get('/loja')->assertDontSee('Esgotado');

        Product::factory()->outOfStock()->create(['name' => 'Gone Cap']);
        $this->get('/loja')->assertSee('Gone Cap')->assertSee('Esgotado');
    });

    it('filters products by category', function (): void {
        $shirts = ProductCategory::factory()->create(['name' => 'Shirts', 'slug' => 'shirts']);
        Product::factory()->for($shirts, 'category')->create(['name' => 'Band Shirt']);
        Product::factory()->create(['name' => 'Band Sticker']);

        $this->get('/loja?categoria=shirts')
            ->assertOk()
            ->assertSee('Band Shirt')
            ->assertDontSee('Band Sticker');
    });

    it('offers only categories that have active products', function (): void {
        $shirts = ProductCategory::factory()->create(['name' => 'Shirts Category']);
        Product::factory()->for($shirts, 'category')->create();
        $hidden = ProductCategory::factory()->create(['name' => 'Hidden Category']);
        Product::factory()->inactive()->for($hidden, 'category')->create();
        ProductCategory::factory()->create(['name' => 'Empty Category']);

        $this->get('/loja')
            ->assertSee('Shirts Category')
            ->assertDontSee('Hidden Category')
            ->assertDontSee('Empty Category');
    });

    it('shows the cover as a WebP thumbnail', function (): void {
        Storage::fake('public');
        $product = Product::factory()->create();
        $product->addMedia(UploadedFile::fake()->image('shirt.jpg', 1200, 1200))->toMediaCollection(Product::PHOTOS);

        $cover = $product->cover();
        Storage::disk('public')->assertExists($cover->getPathRelativeToRoot('thumb'));

        $this->get('/loja')->assertSee($cover->getUrl('thumb'), escape: false);
    });

    it('tells the visitor when the store is empty', function (): void {
        $this->get('/loja')->assertOk()->assertSee('Nenhum produto disponível no momento');
    });
});

describe('product page', function (): void {
    it('links the buy button to WhatsApp with the product filled in', function (): void {
        Setting::write(Setting::STORE_WHATSAPP, '5541999998888');
        $product = Product::factory()->create(['name' => 'Band Cap', 'slug' => 'band-cap', 'price_cents' => 4500]);

        $expected = 'https://wa.me/5541999998888?text='.rawurlencode("Olá! Quero comprar: Band Cap — R$ 45,00\n".route('store.show', $product));

        $this->get('/loja/band-cap')
            ->assertOk()
            ->assertSee('Comprar pelo WhatsApp')
            ->assertSee('href="'.e($expected).'"', escape: false);
    });

    it('fills the WhatsApp message with the chosen variant and skips sold-out variants', function (): void {
        Setting::write(Setting::STORE_WHATSAPP, '5541999998888');
        $product = Product::factory()->create(['name' => 'Band Shirt', 'slug' => 'band-shirt']);
        ProductVariant::factory()->for($product)->outOfStock()->create(['name' => 'P', 'sort_order' => 1]);
        ProductVariant::factory()->for($product)->create(['name' => 'M', 'sort_order' => 2]);
        ProductVariant::factory()->for($product)->create(['name' => 'G', 'sort_order' => 3]);

        $response = $this->get('/loja/band-shirt')->assertOk();

        $urls = $response->viewData('variantPurchaseUrls');
        expect($urls)->toHaveCount(2)
            ->and(rawurldecode($urls->first()))->toContain('Band Shirt (M)')
            ->and(rawurldecode($urls->last()))->toContain('Band Shirt (G)');

        // The pre-selected option is the first one in stock, so the link works even before any click.
        $response->assertSee('href="'.e($urls->first()).'"', escape: false);
    });

    it('sends the buyer to the external store when the product has one', function (): void {
        Setting::write(Setting::STORE_WHATSAPP, '5541999998888');
        Product::factory()->create(['slug' => 'external', 'external_url' => 'https://store.test/item/1']);

        $this->get('/loja/external')
            ->assertSee('href="https://store.test/item/1"', escape: false)
            ->assertDontSee('wa.me')
            ->assertDontSee('Comprar pelo WhatsApp');
    });

    it('does not offer purchase for a product without stock', function (): void {
        Setting::write(Setting::STORE_WHATSAPP, '5541999998888');
        Product::factory()->outOfStock()->create(['slug' => 'sold-out', 'external_url' => 'https://store.test/item/2']);

        $this->get('/loja/sold-out')
            ->assertOk()
            ->assertSee('Esgotado')
            ->assertDontSee('wa.me')
            ->assertDontSee('https://store.test/item/2');
    });

    it('does not offer purchase when every variant is sold out, whatever the product stock says', function (): void {
        Setting::write(Setting::STORE_WHATSAPP, '5541999998888');
        $product = Product::factory()->create(['slug' => 'no-sizes-left', 'stock' => 10]);
        ProductVariant::factory()->for($product)->outOfStock()->count(2)->create();

        $this->get('/loja/no-sizes-left')
            ->assertSee('Esgotado')
            ->assertDontSee('wa.me');
    });

    it('explains that purchase is unavailable when no sales channel is configured', function (): void {
        Product::factory()->create(['slug' => 'no-channel']);

        $this->get('/loja/no-channel')
            ->assertOk()
            ->assertSee('Compra indisponível no momento')
            ->assertDontSee('wa.me');
    });

    it('returns 404 for an inactive product', function (): void {
        Product::factory()->inactive()->create(['slug' => 'inactive']);

        $this->get('/loja/inactive')->assertNotFound();
    });

    it('returns 404 for an unknown product', function (): void {
        $this->get('/loja/does-not-exist')->assertNotFound();
    });

    it('escapes markup in the name and description', function (): void {
        Product::factory()->create(['slug' => 'xss', 'name' => '<b>Shirt</b>', 'description' => '<script>alert(1)</script>']);

        $this->get('/loja/xss')
            ->assertDontSee('<script>alert(1)</script>', escape: false)
            ->assertDontSee('<b>Shirt</b>', escape: false);
    });
});
