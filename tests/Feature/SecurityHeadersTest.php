<?php

it('sends the protective headers on every kind of response', function (string $uri, int $status): void {
    $this->get($uri)
        ->assertStatus($status)
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
        ->assertHeader('Content-Security-Policy', "frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'");
})->with([
    'public page' => ['/', 200],
    'admin login' => ['/admin/login', 200],
    'redirect' => ['/admin', 302],
    'not found' => ['/pagina-que-nao-existe', 404],
    'sitemap' => ['/sitemap.xml', 200],
]);

it('asks browsers to stick to HTTPS only when the request came over HTTPS', function (): void {
    $this->get('http://localhost/contato')->assertHeaderMissing('Strict-Transport-Security');

    $this->get('https://localhost/contato')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});
