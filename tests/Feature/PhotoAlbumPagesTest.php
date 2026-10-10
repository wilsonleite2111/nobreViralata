<?php

use App\Models\PhotoAlbum;
use App\Models\Show;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function addPhotos(PhotoAlbum $album, int $count): void
{
    foreach (range(1, $count) as $number) {
        $album->addMedia(UploadedFile::fake()->image("photo-{$number}.jpg", 1200, 800))
            ->toMediaCollection(PhotoAlbum::PHOTOS);
    }
}

describe('albums list', function (): void {
    it('lists published albums newest first with their cover', function (): void {
        Storage::fake('public');
        PhotoAlbum::factory()->create(['title' => 'Older Album', 'taken_at' => '2026-01-10']);
        $newer = PhotoAlbum::factory()->create(['title' => 'Newer Album', 'taken_at' => '2026-06-10']);
        PhotoAlbum::factory()->unpublished()->create(['title' => 'Draft Album']);
        addPhotos($newer, 2);

        $this->get('/fotos')
            ->assertOk()
            ->assertSeeInOrder(['Newer Album', 'Older Album'])
            ->assertDontSee('Draft Album')
            ->assertSee($newer->cover()->getUrl('thumb'), escape: false);
    });

    it('tells the visitor when there are no albums', function (): void {
        $this->get('/fotos')->assertOk()->assertSee('Nenhum álbum publicado ainda');
    });
});

describe('album page', function (): void {
    it('renders a WebP thumbnail and a large WebP version of every photo', function (): void {
        Storage::fake('public');
        $album = PhotoAlbum::factory()->create(['slug' => 'the-album']);
        addPhotos($album, 3);

        $response = $this->get('/fotos/the-album')->assertOk();

        $photos = $album->getMedia(PhotoAlbum::PHOTOS);
        expect($photos)->toHaveCount(3);

        foreach ($photos as $photo) {
            expect($photo->getUrl('thumb'))->toEndWith('.webp');
            Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('thumb'));
            Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('large'));

            $response->assertSee($photo->getUrl('thumb'), escape: false);
            $response->assertSee(basename($photo->getPath('large')), escape: false);
        }
    });

    it('shows photos in the order set in the admin', function (): void {
        Storage::fake('public');
        $album = PhotoAlbum::factory()->create(['slug' => 'the-album']);
        addPhotos($album, 2);
        [$first, $second] = $album->getMedia(PhotoAlbum::PHOTOS)->all();
        $second->update(['order_column' => 1]);
        $first->update(['order_column' => 2]);

        $this->get('/fotos/the-album')
            ->assertSeeInOrder([$second->getUrl('thumb'), $first->getUrl('thumb')], escape: false);
    });

    it('links to the related show only when that show is published', function (bool $showIsPublished): void {
        $show = Show::factory()->create(['slug' => 'the-gig', 'is_published' => $showIsPublished]);
        PhotoAlbum::factory()->for($show)->create(['slug' => 'the-album']);

        $response = $this->get('/fotos/the-album')->assertOk();

        $showIsPublished
            ? $response->assertSee(route('shows.show', $show), escape: false)
            : $response->assertDontSee(route('shows.show', $show), escape: false);
    })->with([true, false]);

    it('returns 404 for an unpublished album', function (): void {
        PhotoAlbum::factory()->unpublished()->create(['slug' => 'draft-album']);

        $this->get('/fotos/draft-album')->assertNotFound();
    });

    it('returns 404 for an unknown album', function (): void {
        $this->get('/fotos/does-not-exist')->assertNotFound();
    });
});

it('lists a show\'s published albums on the show page', function (): void {
    $show = Show::factory()->create(['slug' => 'the-gig']);
    PhotoAlbum::factory()->for($show)->create(['title' => 'Gig Album']);
    PhotoAlbum::factory()->for($show)->unpublished()->create(['title' => 'Draft Gig Album']);
    PhotoAlbum::factory()->create(['title' => 'Unrelated Album']);

    $this->get('/shows/the-gig')
        ->assertOk()
        ->assertSee('Gig Album')
        ->assertDontSee('Draft Gig Album')
        ->assertDontSee('Unrelated Album');
});
