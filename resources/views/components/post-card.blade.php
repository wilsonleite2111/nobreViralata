@props(['post'])

<article {{ $attributes }}>
    <a href="{{ route('news.show', $post) }}" class="group block">
        <div class="aspect-video overflow-hidden bg-ink-soft">
            @if ($post->coverUrl('card'))
                <img src="{{ $post->coverUrl('card') }}" alt="" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
            @endif
        </div>
        <p class="mt-4 font-display text-sm uppercase tracking-widest text-brand">
            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->isoFormat('D [de] MMMM [de] YYYY') }}</time>
        </p>
        <h2 class="mt-1 font-display text-2xl font-medium uppercase tracking-wide transition group-hover:text-brand">{{ $post->title }}</h2>
        <p class="mt-2 text-white/60">{{ $post->summary() }}</p>
    </a>
</article>
