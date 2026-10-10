<?php

use App\Mail\NewsletterConfirmation;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;

describe('subscribing', function (): void {
    it('stores the address unconfirmed and queues the confirmation e-mail', function (): void {
        Mail::fake();

        $this->from('/')
            ->post('/newsletter', ['email' => 'Fan@Example.com'])
            ->assertRedirectContains('#newsletter')
            ->assertSessionHas('newsletter_status');

        $subscriber = Subscriber::query()->where('email', 'fan@example.com')->sole();

        expect($subscriber->isActive())->toBeFalse()
            ->and($subscriber->token)->toHaveLength(64);

        Mail::assertQueued(
            NewsletterConfirmation::class,
            fn (NewsletterConfirmation $mail): bool => $mail->hasTo('fan@example.com') && $mail->subscriber->is($subscriber),
        );
    });

    it('answers the same way for an address already on the list, without e-mailing it again', function (): void {
        Mail::fake();
        Subscriber::factory()->create(['email' => 'fan@example.com']);

        $this->from('/')
            ->post('/newsletter', ['email' => 'fan@example.com'])
            ->assertRedirectContains('#newsletter')
            ->assertSessionHas('newsletter_status');

        expect(Subscriber::query()->count())->toBe(1);
        Mail::assertNothingQueued();
    });

    it('sends the confirmation again to an address that never confirmed', function (): void {
        Mail::fake();
        Subscriber::factory()->unconfirmed()->create(['email' => 'fan@example.com']);

        $this->post('/newsletter', ['email' => 'fan@example.com']);

        expect(Subscriber::query()->count())->toBe(1);
        Mail::assertQueued(NewsletterConfirmation::class, 1);
    });

    it('asks someone who had left the list to confirm again', function (): void {
        Mail::fake();
        $subscriber = Subscriber::factory()->unsubscribed()->create(['email' => 'fan@example.com']);

        $this->post('/newsletter', ['email' => 'fan@example.com']);

        expect($subscriber->refresh())
            ->confirmed_at->toBeNull()
            ->unsubscribed_at->toBeNull();
        Mail::assertQueued(NewsletterConfirmation::class, 1);
    });

    it('rejects an invalid address with a message in Portuguese', function (string $email, string $message): void {
        Mail::fake();

        $this->from('/')
            ->post('/newsletter', ['email' => $email])
            ->assertRedirectContains('#newsletter')
            ->assertSessionHasErrorsIn('newsletter', ['email' => $message]);

        expect(Subscriber::query()->count())->toBe(0);
        Mail::assertNothingQueued();
    })->with([
        'empty' => ['', 'Informe seu e-mail.'],
        'not an address' => ['fan-at-example', 'Informe um e-mail válido.'],
    ]);

    it('ignores a submission that fills the hidden field, while answering as usual', function (): void {
        Mail::fake();

        $this->post('/newsletter', ['email' => 'bot@example.com', 'website' => 'https://spam.test'])
            ->assertSessionHas('newsletter_status');

        expect(Subscriber::query()->count())->toBe(0);
        Mail::assertNothingQueued();
    });

    it('returns 429 after five attempts in a minute', function (): void {
        Mail::fake();

        foreach (range(1, 5) as $attempt) {
            $this->post('/newsletter', ['email' => "fan{$attempt}@example.com"])->assertRedirect();
        }

        $this->post('/newsletter', ['email' => 'fan6@example.com'])->assertTooManyRequests();
    });
});

describe('confirmation e-mail', function (): void {
    it('carries the confirmation and unsubscribe links', function (): void {
        $subscriber = Subscriber::factory()->unconfirmed()->create();

        $mail = new NewsletterConfirmation($subscriber);

        $mail->assertSeeInHtml(route('newsletter.confirm', $subscriber), escape: false);
        $mail->assertSeeInHtml(route('newsletter.unsubscribe', $subscriber), escape: false);
        $mail->assertSeeInText('Confirmar inscrição');
    });
});

describe('confirming', function (): void {
    it('activates the subscription', function (): void {
        $subscriber = Subscriber::factory()->unconfirmed()->create();

        $this->get("/newsletter/confirmar/{$subscriber->token}")
            ->assertOk()
            ->assertSee('Inscrição confirmada');

        expect($subscriber->refresh()->isActive())->toBeTrue();
    });

    it('keeps the original confirmation date when the link is opened again', function (): void {
        $subscriber = Subscriber::factory()->create(['confirmed_at' => '2026-01-01 10:00:00']);

        $this->get("/newsletter/confirmar/{$subscriber->token}")->assertOk();

        expect($subscriber->refresh()->confirmed_at->toDateTimeString())->toBe('2026-01-01 10:00:00');
    });

    it('returns 404 for an unknown token', function (): void {
        $this->get('/newsletter/confirmar/'.str_repeat('x', 64))->assertNotFound();
    });
});

describe('unsubscribing', function (): void {
    it('asks for confirmation before removing, so a link preview cannot unsubscribe anyone', function (): void {
        $subscriber = Subscriber::factory()->create(['email' => 'fan@example.com']);

        $this->get("/newsletter/sair/{$subscriber->token}")
            ->assertOk()
            ->assertSee('fan@example.com')
            ->assertSee('Sim, cancelar inscrição');

        expect($subscriber->refresh()->isActive())->toBeTrue();
    });

    it('removes the address from the list', function (): void {
        $subscriber = Subscriber::factory()->create();

        $this->delete("/newsletter/sair/{$subscriber->token}")
            ->assertOk()
            ->assertSee('Inscrição cancelada');

        expect($subscriber->refresh())
            ->isActive()->toBeFalse()
            ->unsubscribed_at->not->toBeNull();
    });

    it('returns 404 for an unknown token', function (): void {
        $this->delete('/newsletter/sair/'.str_repeat('x', 64))->assertNotFound();
    });
});
