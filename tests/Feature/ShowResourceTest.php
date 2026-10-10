<?php

use App\Filament\Resources\Shows\Pages\CreateShow;
use App\Filament\Resources\Shows\Pages\EditShow;
use App\Filament\Resources\Shows\Pages\ListShows;
use App\Models\Show;
use App\Models\User;
use App\ShowStatus;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the shows admin', function (): void {
    auth()->logout();

    $this->get('/admin/shows')->assertRedirect('/admin/login');
});

it('lists shows in the admin', function (): void {
    $shows = Show::factory()->count(3)->create();

    Livewire::test(ListShows::class)->assertCanSeeTableRecords($shows);
});

it('creates a show that then appears on the public agenda', function (): void {
    Livewire::test(CreateShow::class)
        ->fillForm([
            'title' => 'Bar do Zé',
            'slug' => 'bar-do-ze',
            'starts_at' => now()->addWeek()->setTime(22, 0)->format('Y-m-d H:i:s'),
            'status' => ShowStatus::Scheduled,
            'venue' => 'Bar do Zé',
            'city' => 'Curitiba',
            'state' => 'PR',
            'ticket_url' => 'https://tickets.test/bar-do-ze',
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('shows', ['slug' => 'bar-do-ze', 'city' => 'Curitiba', 'is_published' => true]);
    $this->get('/shows')->assertSee('Bar do Zé');
});

it('fills the slug from the title when creating', function (): void {
    Livewire::test(CreateShow::class)
        ->fillForm(['title' => 'Festival de Inverno'])
        ->assertSchemaStateSet(['slug' => 'festival-de-inverno']);
});

it('rejects a show without its required fields', function (): void {
    Livewire::test(CreateShow::class)
        ->fillForm(['title' => null, 'slug' => null])
        ->call('create')
        ->assertHasFormErrors([
            'title' => 'required',
            'slug' => 'required',
            'starts_at' => 'required',
            'venue' => 'required',
            'city' => 'required',
            'state' => 'required',
        ]);
});

it('rejects a slug already used by another show', function (): void {
    Show::factory()->create(['slug' => 'taken']);

    Livewire::test(CreateShow::class)
        ->fillForm(['slug' => 'taken'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('rejects a ticket link that is not a web address', function (string $ticketUrl): void {
    Livewire::test(CreateShow::class)
        ->fillForm(['ticket_url' => $ticketUrl])
        ->call('create')
        ->assertHasFormErrors(['ticket_url']);
})->with([
    'not a url' => 'ingressos aqui',
    'script scheme' => 'javascript://alert(1)',
]);

it('updates a show', function (): void {
    $show = Show::factory()->create(['slug' => 'original']);

    Livewire::test(EditShow::class, ['record' => $show->getRouteKey()])
        ->fillForm(['status' => ShowStatus::SoldOut, 'title' => 'Changed Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($show->refresh())
        ->status->toBe(ShowStatus::SoldOut)
        ->title->toBe('Changed Title')
        ->slug->toBe('original');
});
