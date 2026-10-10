<x-layouts.site title="A Banda" description="Conheça a história e os integrantes da banda Nobre Vira Lata.">
    <section class="mx-auto max-w-3xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">A Banda</h1>

        @if (filled($history))
            <x-rich-text :html="$history" class="mt-8" />
        @endif
    </section>

    @if ($members->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-20 lg:px-8">
            <h2 class="font-display text-3xl font-bold uppercase tracking-wide">Integrantes</h2>

            <div class="mt-8 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($members as $member)
                    <article>
                        <div class="aspect-4/5 overflow-hidden bg-ink-soft">
                            @if ($member->photoUrl())
                                <img src="{{ $member->photoUrl() }}" alt="{{ $member->name }}" loading="lazy" class="size-full object-cover">
                            @endif
                        </div>
                        <h3 class="mt-4 font-display text-2xl font-medium uppercase tracking-wide">{{ $member->name }}</h3>
                        <p class="font-display text-sm uppercase tracking-widest text-brand">{{ $member->role }}</p>
                        @if ($member->bio)
                            <p class="mt-3 whitespace-pre-line text-white/70">{{ $member->bio }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.site>
