<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function (): void {
    Route::get('/_test/ip', fn (Request $request): string => (string) $request->ip());
});

it('ignores a forwarded address when no proxy is trusted', function (): void {
    config(['trustedproxy.proxies' => null]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertContent('10.0.0.1');
});

it('uses the forwarded address when the request comes from a trusted proxy', function (): void {
    config(['trustedproxy.proxies' => ['10.0.0.1']]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertContent('203.0.113.9');
});

it('ignores a forwarded address sent by anyone other than the trusted proxy', function (): void {
    config(['trustedproxy.proxies' => ['10.0.0.1']]);

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
        ->get('/_test/ip', ['X-Forwarded-For' => '203.0.113.9'])
        ->assertContent('198.51.100.7');
});

it('reads the trusted proxies from the environment', function (?string $value, mixed $expected): void {
    putenv($value === null ? 'TRUSTED_PROXIES' : "TRUSTED_PROXIES={$value}");
    $_ENV['TRUSTED_PROXIES'] = $_SERVER['TRUSTED_PROXIES'] = $value;

    try {
        expect((require config_path('trustedproxy.php'))['proxies'])->toBe($expected);
    } finally {
        putenv('TRUSTED_PROXIES');
        unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);
    }
})->with([
    'unset' => [null, null],
    'empty' => ['', null],
    'any proxy' => ['*', '*'],
    'list' => ['10.0.0.1, 10.0.0.0/8', ['10.0.0.1', '10.0.0.0/8']],
]);
