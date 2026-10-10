<?php

use Illuminate\Support\Facades\Route;

it('shows a 404 page in Portuguese with the site navigation', function (): void {
    $this->get('/pagina-que-nao-existe')
        ->assertNotFound()
        ->assertSee('Página não encontrada')
        ->assertSee('Próximos shows')
        ->assertSee('<meta name="robots" content="noindex">', escape: false);
});

it('shows a self-contained page in Portuguese for other errors', function (int $status, string $title): void {
    Route::get('/_test/error', fn () => abort($status));

    $this->get('/_test/error')
        ->assertStatus($status)
        ->assertSee($title)
        ->assertSee('Página inicial')
        ->assertDontSee('livewire', escape: false);
})->with([
    [419, 'Página expirada'],
    [429, 'Muitas tentativas'],
    [500, 'Algo deu errado'],
    [503, 'Voltamos já'],
]);
