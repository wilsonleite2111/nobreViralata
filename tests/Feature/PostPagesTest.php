<?php

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('news list', function (): void {
    it('lists published news newest first', function (): void {
        Post::factory()->create(['title' => 'Older News', 'published_at' => now()->subMonth()]);
        Post::factory()->create(['title' => 'Newer News', 'published_at' => now()->subDay()]);

        $this->get('/noticias')
            ->assertOk()
            ->assertSeeInOrder(['Newer News', 'Older News']);
    });

    it('does not list unpublished or scheduled news', function (): void {
        Post::factory()->unpublished()->create(['title' => 'Draft News']);
        Post::factory()->scheduled()->create(['title' => 'Future News']);

        $this->get('/noticias')
            ->assertOk()
            ->assertDontSee('Draft News')
            ->assertDontSee('Future News')
            ->assertSee('Nenhuma notícia publicada ainda');
    });

    it('lists scheduled news once its date arrives', function (): void {
        Post::factory()->create(['title' => 'Future News', 'published_at' => '2026-12-01 09:00:00']);

        $this->travelTo('2026-12-01 08:59:00');
        $this->get('/noticias')->assertDontSee('Future News');

        $this->travelTo('2026-12-01 09:00:00');
        $this->get('/noticias')->assertSee('Future News');
    });

    it('shows the excerpt, or the start of the text when there is none', function (): void {
        Post::factory()->create(['excerpt' => 'A hand-written summary.']);
        Post::factory()->create(['excerpt' => null, 'body' => '<p>Body <strong>opening</strong> words.</p>']);

        $this->get('/noticias')
            ->assertSee('A hand-written summary.')
            ->assertSee('Body opening words.');
    });

    it('shows the cover as a WebP card image', function (): void {
        Storage::fake('public');
        $post = Post::factory()->create();
        $post->addMedia(UploadedFile::fake()->image('cover.jpg', 1600, 900))->toMediaCollection(Post::COVER);

        $cover = $post->getFirstMedia(Post::COVER);
        Storage::disk('public')->assertExists($cover->getPathRelativeToRoot('card'));

        $this->get('/noticias')->assertSee($cover->getUrl('card'), escape: false);
    });
});

describe('news page', function (): void {
    it('renders the formatted text', function (): void {
        Post::factory()->create([
            'title' => 'New Single Out',
            'slug' => 'new-single-out',
            'body' => '<h2>Listen now</h2><p>Out on <a href="https://example.test/single">every platform</a>.</p>',
        ]);

        $this->get('/noticias/new-single-out')
            ->assertOk()
            ->assertSee('New Single Out')
            ->assertSee('<h2>Listen now</h2>', escape: false)
            ->assertSee('href="https://example.test/single"', escape: false);
    });

    it('strips scripts from the text', function (): void {
        Post::factory()->create([
            'slug' => 'unsafe',
            'body' => '<p>Safe text</p><script>alert(1)</script><a href="javascript:alert(2)">link</a>',
        ]);

        $this->get('/noticias/unsafe')
            ->assertOk()
            ->assertSee('Safe text')
            ->assertDontSee('alert(1)', escape: false)
            ->assertDontSee('javascript:', escape: false);
    });

    it('escapes markup in the title', function (): void {
        Post::factory()->create(['slug' => 'title-xss', 'title' => '<script>alert(1)</script>']);

        $this->get('/noticias/title-xss')->assertDontSee('<script>alert(1)</script>', escape: false);
    });

    it('returns 404 for news that is not visible yet', function (string $state): void {
        Post::factory()->{$state}()->create(['slug' => 'hidden']);

        $this->get('/noticias/hidden')->assertNotFound();
    })->with(['unpublished', 'scheduled']);

    it('returns 404 for unknown news', function (): void {
        $this->get('/noticias/does-not-exist')->assertNotFound();
    });
});
