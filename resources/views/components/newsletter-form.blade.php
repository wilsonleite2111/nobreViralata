<section id="newsletter" {{ $attributes->merge(['class' => 'scroll-mt-24 border-y border-white/10 bg-ink-soft']) }}>
    <div class="mx-auto max-w-3xl px-4 py-16 text-center lg:px-8">
        <h2 class="font-display text-3xl font-bold uppercase tracking-wide lg:text-4xl">Receba as novidades</h2>
        <p class="mt-3 text-lg text-white/70">Shows, vídeos e lançamentos direto no seu e-mail.</p>

        @if (session('newsletter_status'))
            <p class="mt-8 border border-brand px-6 py-4 text-lg text-white" role="status">{{ session('newsletter_status') }}</p>
        @else
            <form method="POST" action="{{ route('newsletter.store') }}" class="mt-8 flex flex-col gap-3 sm:flex-row">
                @csrf

                {{-- Honeypot: hidden from people, filled in by bots. --}}
                <div class="hidden" aria-hidden="true">
                    <label for="newsletter-website">Não preencha este campo</label>
                    <input type="text" id="newsletter-website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <label for="newsletter-email" class="sr-only">Seu e-mail</label>
                <input
                    type="email"
                    id="newsletter-email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    maxlength="255"
                    autocomplete="email"
                    placeholder="seu@email.com"
                    class="min-w-0 flex-1 border border-white/30 bg-ink px-4 py-3 text-white placeholder:text-white/40 focus:border-brand focus:outline-none"
                    @error('email', 'newsletter') aria-invalid="true" aria-describedby="newsletter-email-error" @enderror
                >
                <button type="submit" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                    Inscrever
                </button>
            </form>

            @error('email', 'newsletter')
                <p id="newsletter-email-error" class="mt-3 text-left text-red-400">{{ $message }}</p>
            @enderror

            <p class="mt-4 text-sm text-white/60">
                Você confirma a inscrição por e-mail e pode sair da lista quando quiser.
                <a href="{{ route('privacy') }}" class="underline hover:text-brand">Política de privacidade</a>.
            </p>
        @endif
    </div>
</section>
