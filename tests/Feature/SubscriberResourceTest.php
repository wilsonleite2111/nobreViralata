<?php

use App\Filament\Resources\Subscribers\Pages\ManageSubscribers;
use App\Models\Subscriber;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the subscribers admin', function (): void {
    auth()->logout();

    $this->get('/admin/subscribers')->assertRedirect('/admin/login');
});

it('lists subscribers with their status', function (): void {
    $confirmed = Subscriber::factory()->create();
    $pending = Subscriber::factory()->unconfirmed()->create();
    $gone = Subscriber::factory()->unsubscribed()->create();

    Livewire::test(ManageSubscribers::class)
        ->assertCanSeeTableRecords([$confirmed, $pending, $gone])
        ->assertTableColumnStateSet('status', 'Confirmado', $confirmed)
        ->assertTableColumnStateSet('status', 'Aguardando confirmação', $pending)
        ->assertTableColumnStateSet('status', 'Saiu da lista', $gone);
});

it('filters subscribers by status', function (string $filter, string $expected): void {
    $subscribers = [
        'confirmed' => Subscriber::factory()->create(),
        'pending' => Subscriber::factory()->unconfirmed()->create(),
        'unsubscribed' => Subscriber::factory()->unsubscribed()->create(),
    ];

    Livewire::test(ManageSubscribers::class)
        ->filterTable('status', $filter)
        ->assertCanSeeTableRecords([$subscribers[$expected]])
        ->assertCanNotSeeTableRecords(collect($subscribers)->except($expected)->all());
})->with([
    ['confirmed', 'confirmed'],
    ['pending', 'pending'],
    ['unsubscribed', 'unsubscribed'],
]);

it('does not offer creating a subscriber by hand', function (): void {
    Livewire::test(ManageSubscribers::class)->assertActionDoesNotExist('create');
});

it('deletes a subscriber', function (): void {
    $subscriber = Subscriber::factory()->create();

    Livewire::test(ManageSubscribers::class)->callTableAction('delete', $subscriber);

    $this->assertModelMissing($subscriber);
});
