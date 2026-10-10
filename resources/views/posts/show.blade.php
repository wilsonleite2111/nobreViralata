<x-layouts.site :title="$post->title" :description="$post->summary()">
    <article class="mx-auto max-w-3xl px-4 py-12 lg:px-8 lg:py-20">
        <a href="{{ route('news.index') }}" class="font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            &larr; Todas as notícias
        </a>

        <p class="mt-8 font-display text-lg font-medium uppercase tracking-widest text-brand">
            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->isoFormat('D [de] MMMM [de] YYYY') }}</time>
        </p>

        <h1 class="mt-2 font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">{{ $post->title }}</h1>

        @if ($post->coverUrl('large'))
            <img src="{{ $post->coverUrl('large') }}" alt="" class="mt-8 w-full">
        @endif

        <x-rich-text :html="$post->body" class="mt-8" />
    </article>
</x-layouts.site>
