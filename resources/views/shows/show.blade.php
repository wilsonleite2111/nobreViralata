<x-layouts.site :title="$show->title" :description="$show->title.' — '.$show->venue.', '.$show->location().', '.$show->starts_at->format('d/m/Y').'.'">
    <x-structured-data :data="$show->toStructuredData()" />

    <article class="mx-auto max-w-3xl px-4 py-12 lg:px-8 lg:py-20">
        <a href="{{ $show->isUpcoming() ? route('shows.index') : route('shows.past') }}" class="font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            &larr; {{ $show->isUpcoming() ? 'Próximos shows' : 'Shows anteriores' }}
        </a>

        <p class="mt-8 font-display text-lg font-medium uppercase tracking-widest text-brand">
            <time datetime="{{ $show->starts_at->toIso8601String() }}">{{ $show->starts_at->isoFormat('dddd, D [de] MMMM [de] YYYY [às] HH[h]mm') }}</time>
        </p>

        <h1 class="mt-2 font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">{{ $show->title }}</h1>

        @if ($show->status !== \App\ShowStatus::Scheduled)
            <p class="mt-4 inline-block border border-white/30 px-3 py-1 font-display text-sm uppercase tracking-widest text-white/70">{{ $show->status->getLabel() }}</p>
        @endif

        <dl class="mt-8 space-y-4 border-y border-white/10 py-6 text-lg">
            <div>
                <dt class="font-display text-sm uppercase tracking-widest text-white/50">Local</dt>
                <dd>{{ $show->venue }}</dd>
            </div>
            <div>
                <dt class="font-display text-sm uppercase tracking-widest text-white/50">Endereço</dt>
                <dd>
                    @if ($show->address)
                        {{ $show->address }} —
                    @endif
                    {{ $show->location() }}
                </dd>
            </div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-4">
            @if ($show->hasTicketsOnSale())
                <a href="{{ $show->ticket_url }}" target="_blank" rel="noopener noreferrer" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                    Comprar ingressos
                </a>
            @endif

            <a href="{{ $show->mapUrl() }}" target="_blank" rel="noopener noreferrer" class="border border-white/30 px-8 py-3 font-display font-medium uppercase tracking-widest transition hover:border-brand hover:text-brand">
                Ver no mapa
            </a>
        </div>

        @if ($show->description)
            <div class="mt-10 whitespace-pre-line text-lg leading-relaxed text-white/80">{{ $show->description }}</div>
        @endif
    </article>

    @if ($albums->isNotEmpty() || $videos->isNotEmpty())
        <div class="mx-auto max-w-7xl space-y-16 px-4 pb-20 lg:px-8">
            @if ($albums->isNotEmpty())
                <section>
                    <h2 class="font-display text-3xl font-bold uppercase tracking-wide">Fotos do show</h2>
                    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($albums as $album)
                            <x-album-card :album="$album" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($videos->isNotEmpty())
                <section>
                    <h2 class="font-display text-3xl font-bold uppercase tracking-wide">Vídeos do show</h2>
                    <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($videos as $video)
                            <x-video-card :video="$video" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    @endif
</x-layouts.site>
