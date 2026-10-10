@props(['paginator', 'previous' => 'Anterior', 'next' => 'Próxima'])

@if ($paginator->hasPages())
    <nav {{ $attributes->merge(['class' => 'mt-8 flex justify-between font-display text-sm font-medium uppercase tracking-widest']) }} aria-label="Paginação">
        @if ($paginator->onFirstPage())
            <span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="transition hover:text-brand">&larr; {{ $previous }}</a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="transition hover:text-brand">{{ $next }} &rarr;</a>
        @endif
    </nav>
@endif
