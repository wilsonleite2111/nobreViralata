<?php

it('explains what data the site collects', function (): void {
    $this->get('/privacidade')
        ->assertOk()
        ->assertSee('Política de privacidade')
        ->assertSee('Newsletter:')
        ->assertSee('Formulário de contato:')
        ->assertSee('Cookies');
});

it('links to the privacy policy from the footer and from both forms', function (string $uri, int $expectedLinks): void {
    $content = $this->get($uri)->assertOk()->getContent();

    expect(substr_count($content, 'href="'.route('privacy').'"'))->toBe($expectedLinks);
})->with([
    'footer only' => ['/shows', 1],
    'home, with the newsletter form' => ['/', 2],
    'contact form' => ['/contato', 2],
]);
