<?php

use App\Models\PhotoAlbum;
use App\Models\Post;
use App\Models\Product;
use App\Models\Show;

it('lists the main pages and every published item', function (): void {
    $show = Show::factory()->create();
    $post = Post::factory()->create();
    $album = PhotoAlbum::factory()->create();
    $product = Product::factory()->create();

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $locations = array_map(strval(...), simplexml_load_string($response->getContent())->xpath('//*[local-name()="loc"]'));

    expect($locations)->toContain(
        route('home'),
        route('shows.index'),
        route('store.index'),
        route('contact'),
        route('shows.show', $show),
        route('news.show', $post),
        route('photos.show', $album),
        route('store.show', $product),
    );
});

it('leaves out everything the public cannot open', function (): void {
    $hidden = [
        route('shows.show', Show::factory()->unpublished()->create()),
        route('news.show', Post::factory()->unpublished()->create()),
        route('news.show', Post::factory()->scheduled()->create()),
        route('photos.show', PhotoAlbum::factory()->unpublished()->create()),
        route('store.show', Product::factory()->inactive()->create()),
    ];

    $content = $this->get('/sitemap.xml')->assertOk()->getContent();

    foreach ($hidden as $url) {
        expect($content)->not->toContain($url);
    }

    expect($content)->not->toContain('/admin')->not->toContain('/newsletter');
});

it('tells crawlers where the sitemap is and to stay out of private areas', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Disallow: /newsletter')
        ->assertSee('Sitemap: '.route('sitemap'));
});
