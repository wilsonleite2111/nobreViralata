<x-layouts.site>
    <section class="mx-auto max-w-7xl px-4 py-10 lg:px-8 lg:py-16">
        <img src="{{ asset('images/banda.jpg') }}" alt="Os cinco integrantes da banda Nobre Vira Lata no palco" width="1088" height="574" class="mx-auto w-full max-w-5xl">

        <div class="mt-10 text-center">
            <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">
                Nobre <span class="text-brand">Vira Lata</span>
            </h1>

            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('shows.index') }}" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                    Próximos shows
                </a>
                <a href="{{ route('contact') }}" class="border border-white/30 px-8 py-3 font-display font-medium uppercase tracking-widest transition hover:border-brand hover:text-brand">
                    Contrate a banda
                </a>
            </div>
        </div>
    </section>
</x-layouts.site>
