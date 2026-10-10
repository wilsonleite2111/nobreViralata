<?php

use App\Models\Setting;

it('normalizes a Brazilian mobile number to wa.me digits', function (string $value, string $expected): void {
    expect(Setting::normalizeWhatsapp($value))->toBe($expected);
})->with([
    'formatted with area code' => ['(41) 99999-8888', '5541999998888'],
    'digits with area code' => ['41999998888', '5541999998888'],
    'with country code' => ['+55 41 99999-8888', '5541999998888'],
    'landline with area code' => ['(41) 3333-4444', '554133334444'],
]);

it('rejects a number that cannot be a Brazilian phone', function (string $value): void {
    expect(Setting::normalizeWhatsapp($value))->toBeNull();
})->with([
    'empty' => '',
    'no area code' => '99999-8888',
    'too long' => '5541999998888123',
    'text' => 'meu zap',
    'other country code with full length' => '1541999998888',
]);
