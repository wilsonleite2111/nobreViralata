<?php

use App\Models\Show;

describe('upcoming shows', function (): void {
    it('lists published upcoming shows soonest first', function (): void {
        Show::factory()->create(['title' => 'Later Gig', 'starts_at' => now()->addMonth()]);
        Show::factory()->create(['title' => 'Sooner Gig', 'starts_at' => now()->addWeek()]);

        $this->get('/shows')
            ->assertOk()
            ->assertSeeInOrder(['Sooner Gig', 'Later Gig']);
    });

    it('does not list unpublished or past shows', function (): void {
        Show::factory()->unpublished()->create(['title' => 'Draft Gig']);
        Show::factory()->past()->create(['title' => 'Old Gig']);

        $this->get('/shows')
            ->assertOk()
            ->assertDontSee('Draft Gig')
            ->assertDontSee('Old Gig')
            ->assertSee('Nenhum show agendado no momento');
    });

    it('keeps a show listed until the end of its day', function (): void {
        $this->travelTo('2026-11-20 23:30:00');
        Show::factory()->create(['title' => 'Tonight Gig', 'starts_at' => '2026-11-20 21:00:00']);

        $this->get('/shows')->assertSee('Tonight Gig');
    });

    it('offers tickets only for scheduled shows', function (): void {
        Show::factory()->create(['ticket_url' => 'https://tickets.test/on-sale']);
        Show::factory()->soldOut()->create(['ticket_url' => 'https://tickets.test/sold-out']);
        Show::factory()->cancelled()->create(['ticket_url' => 'https://tickets.test/cancelled']);

        $this->get('/shows')
            ->assertSee('https://tickets.test/on-sale')
            ->assertDontSee('https://tickets.test/sold-out')
            ->assertDontSee('https://tickets.test/cancelled')
            ->assertSee('Esgotado')
            ->assertSee('Cancelado');
    });
});

describe('past shows', function (): void {
    it('moves a show to the past list the day after it happens', function (): void {
        $this->travelTo('2026-11-20 12:00:00');
        Show::factory()->create(['title' => 'Friday Gig', 'starts_at' => '2026-11-20 21:00:00']);

        $this->get('/shows/anteriores')->assertDontSee('Friday Gig');

        $this->travelTo('2026-11-21 00:05:00');

        $this->get('/shows')->assertDontSee('Friday Gig');
        $this->get('/shows/anteriores')->assertOk()->assertSee('Friday Gig');
    });

    it('lists past shows most recent first and hides unpublished ones', function (): void {
        Show::factory()->create(['title' => 'Older Gig', 'starts_at' => now()->subMonths(2)]);
        Show::factory()->create(['title' => 'Recent Gig', 'starts_at' => now()->subWeek()]);
        Show::factory()->past()->unpublished()->create(['title' => 'Draft Gig']);

        $this->get('/shows/anteriores')
            ->assertSeeInOrder(['Recent Gig', 'Older Gig'])
            ->assertDontSee('Draft Gig');
    });

    it('does not offer tickets for a past show', function (): void {
        Show::factory()->past()->create(['ticket_url' => 'https://tickets.test/expired']);

        $this->get('/shows/anteriores')->assertDontSee('https://tickets.test/expired');
    });
});

describe('show page', function (): void {
    it('renders the show details with event structured data', function (): void {
        $show = Show::factory()->create([
            'title' => 'Rock na Praça',
            'slug' => 'rock-na-praca',
            'starts_at' => now()->addWeek()->setTime(21, 0),
            'venue' => 'Praça Central',
            'city' => 'Curitiba',
            'state' => 'PR',
        ]);

        $this->get('/shows/rock-na-praca')
            ->assertOk()
            ->assertSee('Rock na Praça')
            ->assertSee('Praça Central')
            ->assertSee('Curitiba/PR')
            ->assertSee('"@type":"MusicEvent"', escape: false)
            ->assertSee('"startDate":"'.$show->starts_at->toIso8601String().'"', escape: false)
            ->assertSee('https://schema.org/EventScheduled', escape: false);
    });

    it('marks a cancelled show as cancelled in the structured data', function (): void {
        Show::factory()->cancelled()->create(['slug' => 'cancelled-gig']);

        $this->get('/shows/cancelled-gig')
            ->assertSee('https://schema.org/EventCancelled', escape: false)
            ->assertSee('Cancelado');
    });

    it('returns 404 for an unpublished show', function (): void {
        Show::factory()->unpublished()->create(['slug' => 'draft-gig']);

        $this->get('/shows/draft-gig')->assertNotFound();
    });

    it('returns 404 for an unknown show', function (): void {
        $this->get('/shows/does-not-exist')->assertNotFound();
    });

    it('escapes markup in the description', function (): void {
        Show::factory()->create(['slug' => 'xss-gig', 'description' => '<script>alert(1)</script>']);

        $this->get('/shows/xss-gig')
            ->assertDontSee('<script>alert(1)</script>', escape: false)
            ->assertSee('<script>alert(1)</script>');
    });
});
