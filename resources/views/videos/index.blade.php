<x-layouts.site title="Vídeos" description="Clipes, shows ao vivo e bastidores da banda Nobre Vira Lata.">
    <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Vídeos</h1>

        <nav class="mt-8 flex flex-wrap gap-3" aria-label="Categorias">
            <a
                href="{{ route('videos.index') }}"
                @class([
                    'border px-5 py-2 font-display text-sm font-medium uppercase tracking-widest transition',
                    'border-brand bg-brand text-ink' => $category === null,
                    'border-white/30 hover:border-brand hover:text-brand' => $category !== null,
                ])
                @if ($category === null) aria-current="page" @endif
            >Todos</a>

            @foreach (\App\VideoCategory::cases() as $option)
                <a
                    href="{{ route('videos.index', ['categoria' => $option->value]) }}"
                    @class([
                        'border px-5 py-2 font-display text-sm font-medium uppercase tracking-widest transition',
                        'border-brand bg-brand text-ink' => $category === $option,
                        'border-white/30 hover:border-brand hover:text-brand' => $category !== $option,
                    ])
                    @if ($category === $option) aria-current="page" @endif
                >{{ $option->getLabel() }}</a>
            @endforeach
        </nav>

        @if ($videos->isEmpty())
            <p class="py-12 text-lg text-white/60">Nenhum vídeo publicado aqui ainda. Volte em breve!</p>
        @else
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($videos as $video)
                    <x-video-card :video="$video" />
                @endforeach
            </div>
        @endif

        <x-simple-pagination :paginator="$videos" previous="Mais recentes" next="Mais antigos" />
    </section>
</x-layouts.site>
