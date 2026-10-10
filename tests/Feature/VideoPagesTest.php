<?php

use App\Models\Show;
use App\Models\Video;
use App\VideoCategory;

it('lists published videos newest first', function (): void {
    Video::factory()->create(['title' => 'Older Video', 'published_at' => '2026-01-10']);
    Video::factory()->create(['title' => 'Newer Video', 'published_at' => '2026-06-10']);
    Video::factory()->unpublished()->create(['title' => 'Draft Video']);

    $this->get('/videos')
        ->assertOk()
        ->assertSeeInOrder(['Newer Video', 'Older Video'])
        ->assertDontSee('Draft Video');
});

it('shows only the thumbnail until the visitor clicks play', function (): void {
    Video::factory()->create(['youtube_id' => 'dQw4w9WgXcQ']);

    $this->get('/videos')
        ->assertSee('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', escape: false)
        ->assertSee('<template x-if="playing">', escape: false)
        ->assertSeeInOrder(['<template x-if="playing">', 'youtube-nocookie.com/embed/dQw4w9WgXcQ', '</template>'], escape: false);
});

it('filters videos by category', function (): void {
    Video::factory()->create(['title' => 'Stage Video', 'category' => VideoCategory::Live]);
    Video::factory()->create(['title' => 'Music Clip', 'category' => VideoCategory::Clip]);

    $this->get('/videos?categoria=clip')
        ->assertOk()
        ->assertSee('Music Clip')
        ->assertDontSee('Stage Video');
});

it('lists every video when the category is unknown', function (): void {
    Video::factory()->create(['title' => 'Stage Video', 'category' => VideoCategory::Live]);

    $this->get('/videos?categoria=nope')
        ->assertOk()
        ->assertSee('Stage Video');
});

it('tells the visitor when there are no videos', function (): void {
    $this->get('/videos')->assertOk()->assertSee('Nenhum vídeo publicado aqui ainda');
});

it('lists a show\'s published videos on the show page', function (): void {
    $show = Show::factory()->create(['slug' => 'the-gig']);
    Video::factory()->for($show)->create(['title' => 'Gig Video']);
    Video::factory()->for($show)->unpublished()->create(['title' => 'Draft Gig Video']);
    Video::factory()->create(['title' => 'Unrelated Video']);

    $this->get('/shows/the-gig')
        ->assertOk()
        ->assertSee('Gig Video')
        ->assertDontSee('Draft Gig Video')
        ->assertDontSee('Unrelated Video');
});
