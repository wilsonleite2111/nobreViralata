@props(['album'])

@php($cover = $album->cover())

<a href="{{ route('photos.show', $album) }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="aspect-square overflow-hidden bg-ink-soft">
        @if ($cover)
            <img src="{{ $cover->getAvailableUrl(['thumb']) }}" alt="" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
        @endif
    </div>
    <h2 class="mt-3 font-display text-xl font-medium uppercase tracking-wide transition group-hover:text-brand">{{ $album->title }}</h2>
    <p class="text-sm text-white/60">{{ $album->taken_at->isoFormat('D [de] MMMM [de] YYYY') }}</p>
</a>
