@props(['product'])

@php($cover = $product->cover())

<a href="{{ route('store.show', $product) }}" {{ $attributes->merge(['class' => 'group block']) }}>
    <div class="relative aspect-square overflow-hidden bg-ink-soft">
        @if ($cover)
            <img src="{{ $cover->getAvailableUrl(['thumb']) }}" alt="" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
        @endif

        @unless ($product->isInStock())
            <span class="absolute left-3 top-3 bg-ink px-3 py-1 font-display text-sm uppercase tracking-widest text-white/80">Esgotado</span>
        @endunless
    </div>
    <h2 class="mt-3 font-display text-xl font-medium uppercase tracking-wide transition group-hover:text-brand">{{ $product->name }}</h2>
    <p class="text-lg text-brand">{{ $product->formattedPrice() }}</p>
</a>
