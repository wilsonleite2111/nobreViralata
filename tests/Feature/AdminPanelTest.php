<?php

use App\Models\User;
use Filament\Auth\Pages\EditProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('redirects guests from the admin panel to the login page', function (): void {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('shows the admin login page', function (): void {
    $this->get('/admin/login')->assertOk();
});

it('lets an authenticated user open the admin panel', function (): void {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk();
});

it('redirects guests away from the profile page', function (): void {
    $this->get('/admin/profile')->assertRedirect('/admin/login');
});

it('lets a user change their own password after confirming the current one', function (): void {
    $user = User::factory()->create(['password' => 'old-password']);
    $this->actingAs($user)->get('/admin/profile')->assertOk();

    Livewire::test(EditProfile::class)
        ->fillForm([
            'password' => 'new-password-123',
            'passwordConfirmation' => 'new-password-123',
            'currentPassword' => 'old-password',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('new-password-123', $user->refresh()->password))->toBeTrue();
});

it('rejects a password change with the wrong current password', function (): void {
    $user = User::factory()->create(['password' => 'old-password']);
    $this->actingAs($user);

    Livewire::test(EditProfile::class)
        ->fillForm([
            'password' => 'new-password-123',
            'passwordConfirmation' => 'new-password-123',
            'currentPassword' => 'not-the-password',
        ])
        ->call('save')
        ->assertHasFormErrors(['currentPassword']);

    expect(Hash::check('old-password', $user->refresh()->password))->toBeTrue();
});
