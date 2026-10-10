<?php

use App\Models\User;

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
