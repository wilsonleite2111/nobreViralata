<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function contactPayload(array $overrides = []): array
{
    return [
        'name' => 'Maria Produtora',
        'email' => 'maria@example.com',
        'subject' => 'Show em dezembro',
        'message' => 'Gostaria de contratar a banda para um evento.',
        ...$overrides,
    ];
}

it('renders the contact form', function (): void {
    $this->get('/contato')->assertOk()->assertSee('Enviar mensagem');
});

it('stores the message and e-mails it to the configured address', function (): void {
    Mail::fake();
    Setting::write(Setting::CONTACT_EMAIL, 'banda@example.com');

    $this->post('/contato', contactPayload())
        ->assertRedirect('/contato')
        ->assertSessionHas('contact_status');

    $message = ContactMessage::query()->sole();

    expect($message->only(['name', 'email', 'subject', 'message']))->toBe(contactPayload());

    Mail::assertQueued(
        ContactMessageReceived::class,
        fn (ContactMessageReceived $mail): bool => $mail->hasTo('banda@example.com')
            && $mail->hasReplyTo('maria@example.com')
            && $mail->contactMessage->is($message),
    );
});

it('stores the message without e-mailing when no address is configured', function (): void {
    Mail::fake();

    $this->post('/contato', contactPayload())->assertRedirect('/contato');

    expect(ContactMessage::query()->count())->toBe(1);
    Mail::assertNothingQueued();
});

it('shows the confirmation after sending', function (): void {
    Mail::fake();

    $this->followingRedirects()
        ->post('/contato', contactPayload())
        ->assertOk()
        ->assertSee('Mensagem enviada!');
});

it('rejects an incomplete message with messages in Portuguese', function (array $overrides, string $field, string $message): void {
    Mail::fake();

    $this->from('/contato')
        ->post('/contato', contactPayload($overrides))
        ->assertRedirect('/contato')
        ->assertSessionHasErrors([$field => $message]);

    expect(ContactMessage::query()->count())->toBe(0);
    Mail::assertNothingQueued();
})->with([
    'no name' => [['name' => ''], 'name', 'Informe seu nome.'],
    'no email' => [['email' => ''], 'email', 'Informe seu e-mail.'],
    'invalid email' => [['email' => 'maria-at-example'], 'email', 'Informe um e-mail válido.'],
    'no subject' => [['subject' => ''], 'subject', 'Informe o assunto.'],
    'no message' => [['message' => ''], 'message', 'Escreva sua mensagem.'],
    'short message' => [['message' => 'Oi'], 'message', 'Escreva uma mensagem um pouco maior.'],
    'huge message' => [['message' => str_repeat('a', 5001)], 'message', 'A mensagem pode ter no máximo 5000 caracteres.'],
]);

it('ignores a submission that fills the hidden field, while answering as usual', function (): void {
    Mail::fake();
    Setting::write(Setting::CONTACT_EMAIL, 'banda@example.com');

    $this->post('/contato', contactPayload(['website' => 'https://spam.test']))
        ->assertRedirect('/contato')
        ->assertSessionHas('contact_status');

    expect(ContactMessage::query()->count())->toBe(0);
    Mail::assertNothingQueued();
});

it('returns 429 after five messages in a minute', function (): void {
    Mail::fake();

    foreach (range(1, 5) as $attempt) {
        $this->post('/contato', contactPayload())->assertRedirect();
    }

    $this->post('/contato', contactPayload())->assertTooManyRequests();
});

it('escapes the sender\'s text in the notification e-mail', function (): void {
    $message = ContactMessage::factory()->create([
        'name' => 'Maria',
        'subject' => 'Evento',
        'message' => "Primeira linha\n<script>alert(1)</script>",
    ]);

    $mail = new ContactMessageReceived($message);

    $mail->assertSeeInHtml('Primeira linha');
    $mail->assertDontSeeInHtml('<script>alert(1)</script>', escape: false);
    $mail->assertHasSubject('Contato pelo site: Evento');
});

it('returns 429 after thirty messages in a day, even when sent slowly', function (): void {
    Mail::fake();

    foreach (range(1, 30) as $attempt) {
        $this->post('/contato', contactPayload())->assertRedirect();
        $this->travel(61)->seconds();
    }

    $this->post('/contato', contactPayload())->assertTooManyRequests();

    $this->travel(1)->days();
    $this->post('/contato', contactPayload())->assertRedirect();
});
