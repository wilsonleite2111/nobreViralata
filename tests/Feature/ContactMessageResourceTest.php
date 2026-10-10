<?php

use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the contact messages admin', function (): void {
    auth()->logout();

    $this->get('/admin/contact-messages')->assertRedirect('/admin/login');
});

it('lists messages newest first', function (): void {
    $older = ContactMessage::factory()->create(['created_at' => now()->subDay()]);
    $newer = ContactMessage::factory()->create(['created_at' => now()]);

    Livewire::test(ManageContactMessages::class)->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

it('opens a message to read it in full', function (): void {
    $message = ContactMessage::factory()->create(['message' => 'Queremos a banda no nosso festival.']);

    Livewire::test(ManageContactMessages::class)
        ->mountTableAction('view', $message)
        ->assertSet('mountedActions.0.data.message', 'Queremos a banda no nosso festival.');
});

it('does not offer creating or editing a message', function (): void {
    $message = ContactMessage::factory()->create();

    Livewire::test(ManageContactMessages::class)
        ->assertActionDoesNotExist('create')
        ->assertTableActionDoesNotExist('edit', record: $message);
});

it('deletes a message', function (): void {
    $message = ContactMessage::factory()->create();

    Livewire::test(ManageContactMessages::class)->callTableAction('delete', $message);

    $this->assertModelMissing($message);
});
