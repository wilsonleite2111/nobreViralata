<x-layouts.site title="Página não encontrada" :noindex="true">
    <section class="mx-auto max-w-2xl px-4 py-24 text-center lg:px-8 lg:py-32">
        <p class="font-display text-7xl font-bold text-brand lg:text-9xl">404</p>
        <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide lg:text-5xl">Página não encontrada</h1>
        <p class="mt-6 text-lg text-white/70">Esse endereço não existe ou saiu do ar. Que tal ver a agenda de shows?</p>

        <div class="mt-10 flex flex-wrap justify-center gap-4">
            <a href="{{ route('home') }}" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">Página inicial</a>
            <a href="{{ route('shows.index') }}" class="border border-white/30 px-8 py-3 font-display font-medium uppercase tracking-widest transition hover:border-brand hover:text-brand">Próximos shows</a>
        </div>
    </section>
</x-layouts.site>
