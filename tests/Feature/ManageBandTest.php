<?php

use App\Filament\Pages\ManageBand;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

it('redirects guests away from the band history admin', function (): void {
    $this->get('/admin/band')->assertRedirect('/admin/login');
});

it('opens with the saved history', function (): void {
    Setting::write(Setting::BAND_HISTORY, '<p>Existing history.</p>');

    $this->actingAs(User::factory()->create())->get('/admin/band')->assertOk();

    Livewire::test(ManageBand::class)->assertSchemaStateSet(
        fn (array $state): bool => str_contains(json_encode($state['history'], JSON_THROW_ON_ERROR), 'Existing history.'),
    );
});

it('saves the history, which then appears on the band page', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageBand::class)
        ->fillForm(['history' => '<p>Born in a <strong>garage</strong>.</p>'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('História salva');

    expect(Setting::read(Setting::BAND_HISTORY))->toContain('<strong>garage</strong>');
    $this->get('/banda')->assertSee('<strong>garage</strong>', escape: false);
});

it('updates the history instead of duplicating it', function (): void {
    $this->actingAs(User::factory()->create());
    Setting::write(Setting::BAND_HISTORY, '<p>Old.</p>');

    Livewire::test(ManageBand::class)
        ->fillForm(['history' => '<p>New.</p>'])
        ->call('save');

    expect(Setting::query()->where('key', Setting::BAND_HISTORY)->count())->toBe(1)
        ->and(Setting::read(Setting::BAND_HISTORY))->toContain('New.');
});
