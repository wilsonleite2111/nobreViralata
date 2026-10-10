<?php

use App\Filament\Resources\Videos\Pages\CreateVideo;
use App\Filament\Resources\Videos\Pages\EditVideo;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Models\User;
use App\Models\Video;
use App\VideoCategory;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the videos admin', function (): void {
    auth()->logout();

    $this->get('/admin/videos')->assertRedirect('/admin/login');
});

it('lists videos in the admin', function (): void {
    $videos = Video::factory()->count(3)->create();

    Livewire::test(ListVideos::class)->assertCanSeeTableRecords($videos);
});

it('creates a video from a pasted YouTube link', function (): void {
    Livewire::test(CreateVideo::class)
        ->fillForm([
            'title' => 'Ao vivo no Bar do Zé',
            'youtube_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10s',
            'category' => VideoCategory::Live,
            'published_at' => '2026-10-03',
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('videos', [
        'title' => 'Ao vivo no Bar do Zé',
        'youtube_id' => 'dQw4w9WgXcQ',
        'category' => 'live',
        'is_published' => true,
    ]);
    $this->get('/videos')->assertSee('Ao vivo no Bar do Zé');
});

it('rejects a link that is not a YouTube video', function (): void {
    Livewire::test(CreateVideo::class)
        ->fillForm(['youtube_id' => 'https://vimeo.com/123456'])
        ->call('create')
        ->assertHasFormErrors(['youtube_id'])
        ->assertSee('Informe um link válido de vídeo do YouTube.');
});

it('rejects a video without its required fields', function (): void {
    Livewire::test(CreateVideo::class)
        ->fillForm(['published_at' => null])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'youtube_id' => 'required',
            'category' => 'required',
            'published_at' => 'required',
        ]);
});

it('keeps the video id when an existing video is saved again', function (): void {
    $video = Video::factory()->create(['youtube_id' => 'dQw4w9WgXcQ']);

    Livewire::test(EditVideo::class, ['record' => $video->getRouteKey()])
        ->fillForm(['title' => 'Changed Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($video->refresh())
        ->title->toBe('Changed Title')
        ->youtube_id->toBe('dQw4w9WgXcQ');
});
