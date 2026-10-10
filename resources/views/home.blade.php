<x-layouts.site>
    @if ($heroImageUrl)
        <section class="relative flex min-h-[70vh] items-end">
            <img src="{{ $heroImageUrl }}" alt="" fetchpriority="high" class="absolute inset-0 size-full object-cover">
            <div class="absolute inset-0 bg-linear-to-t from-ink via-ink/50 to-transparent"></div>

            <div class="relative mx-auto w-full max-w-7xl px-4 pb-12 pt-40 lg:px-8 lg:pb-20">
                <h1 class="font-display text-5xl font-bold uppercase tracking-wide lg:text-8xl">
                    Nobre <span class="text-brand">Vira Lata</span>
                </h1>

                @if ($tagline)
                    <p class="mt-4 max-w-2xl text-xl text-white/80 lg:text-2xl">{{ $tagline }}</p>
                @endif

                <x-home-actions class="mt-8" />
            </div>
        </section>
    @else
        <section class="mx-auto max-w-7xl px-4 py-10 text-center lg:px-8 lg:py-16">
            <img src="{{ asset('images/banda.jpg') }}" alt="Os cinco integrantes da banda Nobre Vira Lata no palco" width="1088" height="574" fetchpriority="high" class="mx-auto w-full max-w-5xl">

            <h1 class="mt-10 font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">
                Nobre <span class="text-brand">Vira Lata</span>
            </h1>

            @if ($tagline)
                <p class="mx-auto mt-4 max-w-2xl text-xl text-white/80">{{ $tagline }}</p>
            @endif

            <x-home-actions class="mt-8 justify-center" />
        </section>
    @endif

    @if ($shows->isNotEmpty())
        <section class="mx-auto max-w-5xl px-4 py-16 lg:px-8">
            <x-section-heading title="Próximos shows" :href="route('shows.index')" link="Agenda completa" />

            <div class="mt-8 border-t border-white/10">
                @foreach ($shows as $show)
                    <x-show-row :show="$show" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($video)
        <section class="mx-auto max-w-5xl px-4 py-16 lg:px-8">
            <x-section-heading title="Vídeo em destaque" :href="route('videos.index')" link="Todos os vídeos" />
            <x-video-card :video="$video" class="mt-8" />
        </section>
    @endif

    @if ($posts->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
            <x-section-heading title="Notícias" :href="route('news.index')" link="Todas as notícias" />

            <div class="mt-8 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($albums->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
            <x-section-heading title="Fotos" :href="route('photos.index')" link="Todos os álbuns" />

            <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <x-album-card :album="$album" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($products->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-16 lg:px-8">
            <x-section-heading title="Loja" :href="route('store.index')" link="Ver a loja" />

            <div class="mt-8 grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    <x-newsletter-form class="mt-16" />
</x-layouts.site>
