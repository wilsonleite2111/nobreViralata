<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterSubscriptionRequest;
use App\Mail\NewsletterConfirmation;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class NewsletterSubscriptionController extends Controller
{
    /**
     * The answer is the same whether or not the address was already subscribed, so the form cannot be used to probe the list.
     */
    public function store(StoreNewsletterSubscriptionRequest $request): RedirectResponse
    {
        // Bots fill every field, including the hidden one; they get the usual answer and nothing is stored.
        if (! $request->filled('website')) {
            $subscriber = Subscriber::query()->firstOrCreate(['email' => Str::lower($request->validated('email'))]);

            if (! $subscriber->isActive()) {
                $subscriber->update(['confirmed_at' => null, 'unsubscribed_at' => null]);

                // Capped per address, so the form cannot be used to flood someone else's inbox.
                RateLimiter::attempt(
                    'newsletter-confirmation:'.sha1($subscriber->email),
                    maxAttempts: 3,
                    callback: fn () => Mail::to($subscriber->email)->send(new NewsletterConfirmation($subscriber)),
                    decaySeconds: 86400,
                );
            }
        }

        return back()
            ->withFragment('newsletter')
            ->with('newsletter_status', 'Quase lá! Enviamos um e-mail com o link para confirmar sua inscrição.');
    }

    public function confirm(Subscriber $subscriber): View
    {
        $subscriber->confirm();

        return view('newsletter.confirmed');
    }

    public function edit(Subscriber $subscriber): View
    {
        return view('newsletter.unsubscribe', ['subscriber' => $subscriber]);
    }

    public function destroy(Subscriber $subscriber): View
    {
        $subscriber->unsubscribe();

        return view('newsletter.unsubscribed');
    }
}
