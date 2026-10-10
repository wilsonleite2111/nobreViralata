@php
    $lightboxPhotos = $photos->map(fn ($photo) => $photo->getAvailableUrl(['large']))->values();
@endphp

<x-layouts.site :title="$album->title" :description="'Fotos: '.$album->title.' — '.$album->taken_at->format('d/m/Y').'.'" :image="$lightboxPhotos->first()">
    <section
        class="mx-auto max-w-7xl px-4 py-12 lg:px-8 lg:py-20"
        x-data="{
            photos: @js($lightboxPhotos),
            index: null,
            touchStartX: 0,
            open(position) { this.index = position },
            close() { this.index = null },
            next() { this.index = (this.index + 1) % this.photos.length },
            previous() { this.index = (this.index - 1 + this.photos.length) % this.photos.length },
            swipe(touchEndX) {
                const distance = touchEndX - this.touchStartX
                if (Math.abs(distance) < 40) return
                distance < 0 ? this.next() : this.previous()
            },
        }"
        x-on:keydown.escape.window="close()"
        x-on:keydown.arrow-right.window="index !== null && next()"
        x-on:keydown.arrow-left.window="index !== null && previous()"
    >
        <a href="{{ route('photos.index') }}" class="font-display text-sm font-medium uppercase tracking-widest text-white/70 transition hover:text-brand">
            &larr; Todos os álbuns
        </a>

        <h1 class="mt-8 font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">{{ $album->title }}</h1>
        <p class="mt-2 text-white/60">
            {{ $album->taken_at->isoFormat('D [de] MMMM [de] YYYY') }}
            @if ($album->show?->is_published)
                · <a href="{{ route('shows.show', $album->show) }}" class="underline transition hover:text-brand">{{ $album->show->title }}</a>
            @endif
        </p>

        @if ($photos->isEmpty())
            <p class="py-12 text-lg text-white/60">Este álbum ainda não tem fotos.</p>
        @else
            <ul class="mt-10 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($photos as $photo)
                    <li>
                        <button type="button" x-on:click="open({{ $loop->index }})" class="group block aspect-square w-full overflow-hidden bg-ink-soft" aria-label="Ampliar foto {{ $loop->iteration }} de {{ $loop->count }}">
                            <img src="{{ $photo->getAvailableUrl(['thumb']) }}" alt="{{ $album->title }} — foto {{ $loop->iteration }}" loading="lazy" class="size-full object-cover transition duration-300 group-hover:scale-105">
                        </button>
                    </li>
                @endforeach
            </ul>

            <div
                x-show="index !== null"
                x-cloak
                x-transition.opacity
                x-trap.noscroll="index !== null"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/95"
                role="dialog"
                aria-modal="true"
                aria-label="Foto ampliada"
                x-on:click.self="close()"
                x-on:touchstart.passive="touchStartX = $event.changedTouches[0].clientX"
                x-on:touchend.passive="swipe($event.changedTouches[0].clientX)"
            >
                <img x-bind:src="index !== null ? photos[index] : ''" alt="" class="max-h-full max-w-full object-contain">

                <button type="button" x-on:click="close()" class="absolute right-2 top-2 p-3 text-white/80 transition hover:text-brand" aria-label="Fechar">
                    <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
                <button type="button" x-on:click="previous()" class="absolute left-0 top-1/2 -translate-y-1/2 p-3 text-white/80 transition hover:text-brand" aria-label="Foto anterior">
                    <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7" /></svg>
                </button>
                <button type="button" x-on:click="next()" class="absolute right-0 top-1/2 -translate-y-1/2 p-3 text-white/80 transition hover:text-brand" aria-label="Próxima foto">
                    <svg class="size-10" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>

                <p class="absolute bottom-3 left-1/2 -translate-x-1/2 font-display text-sm tracking-widest text-white/70" x-text="index !== null ? `${index + 1} / ${photos.length}` : ''"></p>
            </div>
        @endif
    </section>
</x-layouts.site>
