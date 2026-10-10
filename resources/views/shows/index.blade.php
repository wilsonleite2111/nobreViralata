<x-layouts.site title="Shows" description="Agenda de shows da banda Nobre Vira Lata: datas, locais e ingressos.">
    <section class="mx-auto max-w-5xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Próximos shows</h1>

        <div class="mt-8 border-t border-white/10">
            @forelse ($shows as $show)
                <x-show-row :show="$show" />
            @empty
                <p class="py-12 text-lg text-white/60">Nenhum show agendado no momento. Volte em breve!</p>
            @endforelse
        </div>

        <a href="{{ route('shows.past') }}" class="mt-10 inline-block font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            Ver shows anteriores &rarr;
        </a>
    </section>
</x-layouts.site>
