<?php

it('renders the home page with the site layout', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('Nobre Vira Lata')
        ->assertSee('images/logo-horizontal.jpg')
        ->assertSee('Próximos shows')
        ->assertSee('@bandanobreviralata');
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
