<?php

it('renders the home page with the site layout', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('Soul True Noise')
        ->assertSee('images/logo.jpg')
        ->assertSee('Próximos shows')
        ->assertSee('@stn_soul_true_noise');
});

it('renders every navigation page', function (string $uri, string $title): void {
    $this->get($uri)
        ->assertOk()
        ->assertSee($title);
})->with([
    ['/shows', 'Shows'],
    ['/noticias', 'Notícias'],
    ['/fotos', 'Fotos'],
    ['/videos', 'Vídeos'],
    ['/banda', 'A Banda'],
    ['/loja', 'Loja'],
    ['/contato', 'Contato'],
]);

it('marks the current navigation item', function (): void {
    $this->get('/shows')->assertSee('aria-current="page"', escape: false);
    $this->get('/')->assertDontSee('aria-current="page"', escape: false);
});
