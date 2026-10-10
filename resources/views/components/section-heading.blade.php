@props(['title', 'href' => null, 'link' => 'Ver todos'])

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-end justify-between gap-4']) }}>
    <h2 class="font-display text-3xl font-bold uppercase tracking-wide lg:text-4xl">{{ $title }}</h2>

    @if ($href)
        <a href="{{ $href }}" class="font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">{{ $link }} &rarr;</a>
    @endif
</div>
