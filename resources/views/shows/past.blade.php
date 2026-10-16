<x-layouts.site title="Shows anteriores" description="Arquivo de shows já realizados pela banda Soul True Noise.">
    <section class="mx-auto max-w-5xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Shows anteriores</h1>

        <div class="mt-8 border-t border-white/10">
            @forelse ($shows as $show)
                <x-show-row :show="$show" />
            @empty
                <p class="py-12 text-lg text-white/60">Ainda não há shows anteriores por aqui.</p>
            @endforelse
        </div>

        <x-simple-pagination :paginator="$shows" previous="Mais recentes" next="Mais antigos" />

        <a href="{{ route('shows.index') }}" class="mt-10 inline-block font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            &larr; Próximos shows
        </a>
    </section>
</x-layouts.site>
