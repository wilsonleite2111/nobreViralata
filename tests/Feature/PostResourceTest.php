<?php

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the news admin', function (): void {
    auth()->logout();

    $this->get('/admin/posts')->assertRedirect('/admin/login');
});

it('lists news in the admin', function (): void {
    $posts = Post::factory()->count(3)->create();

    Livewire::test(ListPosts::class)->assertCanSeeTableRecords($posts);
});

it('creates news with a cover that then appears on the site', function (): void {
    Storage::fake('public');

    Livewire::test(CreatePost::class)
        ->fillForm([
            'title' => 'Novo show confirmado',
            'slug' => 'novo-show-confirmado',
            'published_at' => now()->subHour()->format('Y-m-d H:i:s'),
            'is_published' => true,
            'cover' => UploadedFile::fake()->image('cover.jpg', 1600, 900),
            'body' => '<p>Vai ser <strong>demais</strong>.</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::query()->where('slug', 'novo-show-confirmado')->sole();
    $cover = $post->getFirstMedia(Post::COVER);

    expect($cover)->not->toBeNull();
    Storage::disk('public')->assertExists($cover->getPathRelativeToRoot('card'));
    $this->get('/noticias')->assertSee('Novo show confirmado');
    $this->get('/noticias/novo-show-confirmado')->assertOk()->assertSee('<strong>demais</strong>', escape: false);
});

it('fills the slug from the title when creating', function (): void {
    Livewire::test(CreatePost::class)
        ->fillForm(['title' => 'Clipe novo no ar'])
        ->assertSchemaStateSet(['slug' => 'clipe-novo-no-ar']);
});

it('rejects news without its required fields', function (): void {
    Livewire::test(CreatePost::class)
        ->fillForm(['title' => null, 'slug' => null, 'published_at' => null, 'body' => null])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'slug' => 'required',
            'published_at' => 'required',
            'body',
        ]);
});

it('rejects a slug already used by other news', function (): void {
    Post::factory()->create(['slug' => 'taken']);

    Livewire::test(CreatePost::class)
        ->fillForm(['slug' => 'taken'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('keeps the text when existing news is saved again', function (): void {
    $post = Post::factory()->create(['body' => '<p>Original <strong>text</strong>.</p>']);

    Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
        ->fillForm(['title' => 'Changed Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())
        ->title->toBe('Changed Title')
        ->body->toContain('<strong>text</strong>');
});
