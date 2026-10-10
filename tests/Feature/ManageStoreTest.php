<?php

use App\Filament\Pages\ManageStore;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

it('redirects guests away from the store settings', function (): void {
    $this->get('/admin/store')->assertRedirect('/admin/login');
});

it('opens with the saved number', function (): void {
    Setting::write(Setting::STORE_WHATSAPP, '5541999998888');

    $this->actingAs(User::factory()->create())->get('/admin/store')->assertOk();

    Livewire::test(ManageStore::class)->assertSchemaStateSet(['whatsapp' => '5541999998888']);
});

it('saves the number in wa.me format, which the buy button then uses', function (): void {
    $this->actingAs(User::factory()->create());
    Product::factory()->create(['slug' => 'band-cap']);

    Livewire::test(ManageStore::class)
        ->fillForm(['whatsapp' => '(41) 99999-8888'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('WhatsApp salvo');

    expect(Setting::read(Setting::STORE_WHATSAPP))->toBe('5541999998888');
    $this->get('/loja/band-cap')->assertSee('https://wa.me/5541999998888', escape: false);
});

it('rejects a number without area code', function (): void {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageStore::class)
        ->fillForm(['whatsapp' => '99999-8888'])
        ->call('save')
        ->assertHasFormErrors(['whatsapp'])
        ->assertSee('Informe um número de celular brasileiro com DDD.');

    expect(Setting::read(Setting::STORE_WHATSAPP))->toBeNull();
});

it('clears the number when the field is emptied', function (): void {
    $this->actingAs(User::factory()->create());
    Setting::write(Setting::STORE_WHATSAPP, '5541999998888');

    Livewire::test(ManageStore::class)
        ->fillForm(['whatsapp' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Setting::read(Setting::STORE_WHATSAPP))->toBeNull();
});
