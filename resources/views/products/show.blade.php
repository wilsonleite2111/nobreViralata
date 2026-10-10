@php
    $photoUrls = $photos->map(fn ($photo) => $photo->getAvailableUrl(['large']))->values();
    $buyLabel = $product->external_url ? 'Comprar' : 'Comprar pelo WhatsApp';
@endphp

<x-layouts.site :title="$product->name" :description="$product->name.' — '.$product->formattedPrice().'. Produto oficial da banda Nobre Vira Lata.'">
    <article class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20">
        <a href="{{ route('store.index') }}" class="font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            &larr; Loja
        </a>

        <div class="mt-8 grid gap-10 lg:grid-cols-2">
            <div x-data="{ photo: 0, photos: @js($photoUrls) }">
                <div class="aspect-square overflow-hidden bg-ink-soft">
                    @if ($photos->isNotEmpty())
                        <img src="{{ $photoUrls->first() }}" x-bind:src="photos[photo]" alt="{{ $product->name }}" class="size-full object-contain">
                    @endif
                </div>

                @if ($photos->count() > 1)
                    <ul class="mt-3 grid grid-cols-5 gap-3">
                        @foreach ($photos as $photo)
                            <li>
                                <button
                                    type="button"
                                    x-on:click="photo = {{ $loop->index }}"
                                    x-bind:class="photo === {{ $loop->index }} ? 'border-brand' : 'border-transparent'"
                                    class="block aspect-square w-full overflow-hidden border-2 bg-ink-soft"
                                    aria-label="Ver foto {{ $loop->iteration }} de {{ $loop->count }}"
                                >
                                    <img src="{{ $photo->getAvailableUrl(['thumb']) }}" alt="" loading="lazy" class="size-full object-cover">
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div>
                @if ($product->category)
                    <p class="font-display text-sm uppercase tracking-widest text-white/50">{{ $product->category->name }}</p>
                @endif

                <h1 class="mt-1 font-display text-4xl font-bold uppercase tracking-wide lg:text-5xl">{{ $product->name }}</h1>
                <p class="mt-3 text-3xl text-brand">{{ $product->formattedPrice() }}</p>

                <div class="mt-8" x-data="{ variant: @js($selectedVariantId), urls: @js($variantPurchaseUrls) }">
                    @if ($product->variants->isNotEmpty())
                        <fieldset>
                            <legend class="font-display text-sm uppercase tracking-widest text-white/50">Opções</legend>
                            <div class="mt-3 flex flex-wrap gap-3">
                                @foreach ($product->variants as $variant)
                                    <label @class(['cursor-pointer' => $variant->isInStock(), 'cursor-not-allowed' => ! $variant->isInStock()])>
                                        <input
                                            type="radio"
                                            name="variant"
                                            value="{{ $variant->id }}"
                                            x-model.number="variant"
                                            class="peer sr-only"
                                            @checked($variant->id === $selectedVariantId)
                                            @disabled(! $variant->isInStock())
                                        >
                                        <span class="block min-w-14 border border-white/30 px-4 py-2 text-center font-display font-medium uppercase tracking-widest transition peer-checked:border-brand peer-checked:bg-brand peer-checked:text-ink peer-focus-visible:outline-2 peer-focus-visible:outline-white peer-disabled:text-white/30 peer-disabled:line-through">
                                            {{ $variant->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    <div class="mt-8">
                        @if ($canBePurchased)
                            <a
                                href="{{ $variantPurchaseUrls->get($selectedVariantId) ?? $purchaseUrl }}"
                                @if ($variantPurchaseUrls->isNotEmpty()) x-bind:href="urls[variant]" @endif
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block bg-brand px-10 py-4 font-display text-lg font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark"
                            >{{ $buyLabel }}</a>
                        @elseif (! $product->isInStock())
                            <p class="inline-block border border-white/30 px-10 py-4 font-display text-lg font-medium uppercase tracking-widest text-white/60">Esgotado</p>
                        @else
                            <p class="text-white/70">
                                Compra indisponível no momento.
                                <a href="{{ route('contact') }}" class="text-brand underline hover:text-white">Fale com a gente</a>.
                            </p>
                        @endif
                    </div>
                </div>

                @if ($product->description)
                    <div class="mt-10 whitespace-pre-line border-t border-white/10 pt-8 text-lg leading-relaxed text-white/80">{{ $product->description }}</div>
                @endif
            </div>
        </div>
    </article>
</x-layouts.site>
