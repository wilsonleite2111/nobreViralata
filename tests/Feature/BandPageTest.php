<?php

use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('renders the band history written in the admin', function (): void {
    Setting::write(Setting::BAND_HISTORY, '<h2>How it began</h2><p>Formed in a <strong>garage</strong>.</p>');

    $this->get('/banda')
        ->assertOk()
        ->assertSee('<h2>How it began</h2>', escape: false)
        ->assertSee('<strong>garage</strong>', escape: false);
});

it('strips scripts from the band history', function (): void {
    Setting::write(Setting::BAND_HISTORY, '<p>Safe text</p><script>alert(1)</script><img src="x" onerror="alert(2)">');

    $this->get('/banda')
        ->assertOk()
        ->assertSee('Safe text')
        ->assertDontSee('alert(1)', escape: false)
        ->assertDontSee('onerror', escape: false);
});

it('lists members in the order set in the admin', function (): void {
    Member::factory()->create(['name' => 'Second Member', 'sort_order' => 2]);
    Member::factory()->create(['name' => 'First Member', 'sort_order' => 1, 'role' => 'Bateria']);

    $this->get('/banda')
        ->assertOk()
        ->assertSeeInOrder(['First Member', 'Bateria', 'Second Member']);
});

it('puts a new member at the end of the line-up', function (): void {
    Member::factory()->create(['sort_order' => 4]);

    expect(Member::factory()->create()->sort_order)->toBe(5);
});

it('shows a member photo as a WebP portrait', function (): void {
    Storage::fake('public');
    $member = Member::factory()->create();
    $member->addMedia(UploadedFile::fake()->image('member.jpg', 1200, 1600))->toMediaCollection(Member::PHOTO);

    $photo = $member->getFirstMedia(Member::PHOTO);
    Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('portrait'));

    $this->get('/banda')->assertSee($photo->getUrl('portrait'), escape: false);
});

it('escapes markup in a member bio', function (): void {
    Member::factory()->create(['name' => '<b>Name</b>', 'bio' => '<script>alert(1)</script>']);

    $this->get('/banda')
        ->assertDontSee('<script>alert(1)</script>', escape: false)
        ->assertDontSee('<b>Name</b>', escape: false)
        ->assertSee('<script>alert(1)</script>');
});

it('renders without history or members', function (): void {
    $this->get('/banda')->assertOk()->assertDontSee('Integrantes');
});
