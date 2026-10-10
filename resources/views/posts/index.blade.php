<x-layouts.site title="Notícias" description="Novidades da banda Nobre Vira Lata.">
    <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Notícias</h1>

        @if ($posts->isEmpty())
            <p class="py-12 text-lg text-white/60">Nenhuma notícia publicada ainda. Volte em breve!</p>
        @else
            <div class="mt-10 grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>
        @endif

        <x-simple-pagination :paginator="$posts" previous="Mais recentes" next="Mais antigas" />
    </section>
</x-layouts.site>
