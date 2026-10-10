<x-layouts.site title="Fotos" description="Álbuns de fotos dos shows e bastidores da banda Nobre Vira Lata.">
    <section class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Fotos</h1>

        @if ($albums->isEmpty())
            <p class="py-12 text-lg text-white/60">Nenhum álbum publicado ainda. Volte em breve!</p>
        @else
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albums as $album)
                    <x-album-card :album="$album" />
                @endforeach
            </div>
        @endif

        <x-simple-pagination :paginator="$albums" previous="Mais recentes" next="Mais antigos" />
    </section>
</x-layouts.site>
