@props(['show'])

<article {{ $attributes->merge(['class' => 'flex flex-col gap-4 border-b border-white/10 py-6 sm:flex-row sm:items-center sm:gap-8']) }}>
    <time datetime="{{ $show->starts_at->toIso8601String() }}" class="flex shrink-0 items-baseline gap-3 font-display uppercase sm:w-24 sm:flex-col sm:items-center sm:gap-0">
        <span class="text-4xl font-bold text-brand sm:text-5xl">{{ $show->starts_at->format('d') }}</span>
        <span class="text-sm tracking-widest text-white/70">{{ $show->starts_at->translatedFormat('M Y') }}</span>
    </time>

    <div class="min-w-0 flex-1">
        <h2 class="font-display text-2xl font-medium uppercase tracking-wide">
            <a href="{{ route('shows.show', $show) }}" class="transition hover:text-brand">{{ $show->title }}</a>
        </h2>
        <p class="mt-1 text-white/60">
            {{ $show->venue }} · {{ $show->location() }} · {{ $show->starts_at->isoFormat('ddd, HH[h]mm') }}
        </p>
    </div>

    <div class="flex shrink-0 flex-wrap items-center gap-3">
        @if ($show->status !== \App\ShowStatus::Scheduled)
            <span class="border border-white/30 px-3 py-1 font-display text-sm uppercase tracking-widest text-white/70">{{ $show->status->getLabel() }}</span>
        @endif

        @if ($show->hasTicketsOnSale())
            <a href="{{ $show->ticket_url }}" target="_blank" rel="noopener noreferrer" class="bg-brand px-6 py-2 font-display text-sm font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                Ingressos
            </a>
        @endif

        <a href="{{ route('shows.show', $show) }}" class="border border-white/30 px-6 py-2 font-display text-sm font-medium uppercase tracking-widest transition hover:border-brand hover:text-brand">
            Detalhes
        </a>
    </div>
</article>
