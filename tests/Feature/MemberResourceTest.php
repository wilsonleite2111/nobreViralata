<?php

use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('redirects guests away from the members admin', function (): void {
    auth()->logout();

    $this->get('/admin/members')->assertRedirect('/admin/login');
});

it('lists members in line-up order', function (): void {
    $second = Member::factory()->create(['sort_order' => 2]);
    $first = Member::factory()->create(['sort_order' => 1]);

    Livewire::test(ListMembers::class)->assertCanSeeTableRecords([$first, $second], inOrder: true);
});

it('creates a member with a photo that then appears on the band page', function (): void {
    Storage::fake('public');

    Livewire::test(CreateMember::class)
        ->fillForm([
            'name' => 'Zé da Guitarra',
            'role' => 'Guitarra',
            'bio' => 'Toca desde criança.',
            'photo' => UploadedFile::fake()->image('ze.jpg', 1200, 1600),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $member = Member::query()->where('name', 'Zé da Guitarra')->sole();
    $photo = $member->getFirstMedia(Member::PHOTO);

    expect($photo)->not->toBeNull();
    Storage::disk('public')->assertExists($photo->getPathRelativeToRoot('portrait'));
    $this->get('/banda')->assertSee('Zé da Guitarra')->assertSee('Toca desde criança.');
});

it('saves a new line-up order', function (): void {
    $first = Member::factory()->create(['sort_order' => 1]);
    $second = Member::factory()->create(['sort_order' => 2]);

    Livewire::test(ListMembers::class)->call('reorderTable', [(string) $second->id, (string) $first->id]);

    expect($second->refresh()->sort_order)->toBeLessThan($first->refresh()->sort_order);
});

it('rejects a member without name or role', function (): void {
    Livewire::test(CreateMember::class)
        ->fillForm(['name' => null, 'role' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'role' => 'required']);
});
