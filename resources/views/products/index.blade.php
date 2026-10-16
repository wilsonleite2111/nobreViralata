<x-layouts.site title="Loja" description="Camisetas e produtos oficiais da banda Soul True Noise.">
    <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Loja</h1>

        @if ($categories->isNotEmpty())
            <nav class="mt-8 flex flex-wrap gap-3" aria-label="Categorias">
                <a
                    href="{{ route('store.index') }}"
                    @class([
                        'border px-5 py-2 font-display text-sm font-medium uppercase tracking-widest transition',
                        'border-brand bg-brand text-ink' => $category === null,
                        'border-white/30 hover:border-brand hover:text-brand' => $category !== null,
                    ])
                    @if ($category === null) aria-current="page" @endif
                >Todos</a>

                @foreach ($categories as $option)
                    <a
                        href="{{ route('store.index', ['categoria' => $option->slug]) }}"
                        @class([
                            'border px-5 py-2 font-display text-sm font-medium uppercase tracking-widest transition',
                            'border-brand bg-brand text-ink' => $option->is($category),
                            'border-white/30 hover:border-brand hover:text-brand' => ! $option->is($category),
                        ])
                        @if ($option->is($category)) aria-current="page" @endif
                    >{{ $option->name }}</a>
                @endforeach
            </nav>
        @endif

        @if ($products->isEmpty())
            <p class="py-12 text-lg text-white/60">Nenhum produto disponível no momento. Volte em breve!</p>
        @else
            <div class="mt-10 grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif

        <x-simple-pagination :paginator="$products" />
    </section>
</x-layouts.site>
