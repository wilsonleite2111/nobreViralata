@props(['video'])

<article {{ $attributes }}>
    {{-- The YouTube player only loads after the click, so the page stays light. --}}
    <div x-data="{ playing: false }" class="relative aspect-video overflow-hidden bg-black">
        <template x-if="playing">
            <iframe
                src="{{ $video->embedUrl() }}"
                title="{{ $video->title }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
                class="absolute inset-0 size-full"
            ></iframe>
        </template>

        <button type="button" x-show="! playing" x-on:click="playing = true" class="group absolute inset-0" aria-label="Reproduzir: {{ $video->title }}">
            <img src="{{ $video->thumbnailUrl() }}" alt="" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
            <span class="absolute inset-0 flex items-center justify-center bg-black/20 transition group-hover:bg-black/40">
                <span class="flex size-16 items-center justify-center rounded-full bg-brand text-ink">
                    <svg class="ml-1 size-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z" /></svg>
                </span>
            </span>
        </button>
    </div>

    <h2 class="mt-3 font-display text-xl font-medium uppercase tracking-wide">{{ $video->title }}</h2>
    <p class="text-sm text-white/60">{{ $video->category->getLabel() }} · {{ $video->published_at->isoFormat('D [de] MMMM [de] YYYY') }}</p>
</article>
