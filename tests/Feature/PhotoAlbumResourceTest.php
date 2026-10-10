<?php

use App\Filament\Resources\PhotoAlbums\Pages\CreatePhotoAlbum;
use App\Filament\Resources\PhotoAlbums\Pages\ListPhotoAlbums;
use App\Models\PhotoAlbum;
use App\Models\Show;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the albums admin', function (): void {
    auth()->logout();

    $this->get('/admin/photo-albums')->assertRedirect('/admin/login');
});

it('lists albums in the admin', function (): void {
    $albums = PhotoAlbum::factory()->count(3)->create();

    Livewire::test(ListPhotoAlbums::class)->assertCanSeeTableRecords($albums);
});

it('creates an album whose uploaded photos get WebP versions', function (): void {
    Storage::fake('public');
    $show = Show::factory()->create();

    Livewire::test(CreatePhotoAlbum::class)
        ->fillForm([
            'title' => 'Show no Bar do Zé',
            'slug' => 'show-no-bar-do-ze',
            'taken_at' => '2026-10-03',
            'show_id' => $show->id,
            'photos' => [
                UploadedFile::fake()->image('one.jpg', 2400, 1600),
                UploadedFile::fake()->image('two.png', 800, 1200),
            ],
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $album = PhotoAlbum::query()->where('slug', 'show-no-bar-do-ze')->sole();
    $photos = $album->getMedia(PhotoAlbum::PHOTOS);

    expect($album->show->is($show))->toBeTrue()
        ->and($photos)->toHaveCount(2);

    foreach ($photos as $photo) {
        Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('thumb'));
        Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('large'));
    }

    $this->get('/fotos')->assertSee('Show no Bar do Zé');
});

it('rejects an upload that is not an image', function (): void {
    Storage::fake('public');

    Livewire::test(CreatePhotoAlbum::class)
        ->fillForm([
            'title' => 'Bad Album',
            'slug' => 'bad-album',
            'photos' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')],
        ])
        ->call('create')
        ->assertHasFormErrors(['photos']);

    $this->assertDatabaseMissing('photo_albums', ['slug' => 'bad-album']);
});

it('rejects an album without its required fields', function (): void {
    Livewire::test(CreatePhotoAlbum::class)
        ->fillForm(['title' => null, 'slug' => null, 'taken_at' => null])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'slug' => 'required',
            'taken_at' => 'required',
        ]);
});

it('rejects a slug already used by another album', function (): void {
    PhotoAlbum::factory()->create(['slug' => 'taken']);

    Livewire::test(CreatePhotoAlbum::class)
        ->fillForm(['slug' => 'taken'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});
