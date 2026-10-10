<div {{ $attributes->merge(['class' => 'flex flex-wrap gap-4']) }}>
    <a href="{{ route('shows.index') }}" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
        Próximos shows
    </a>
    <a href="{{ route('contact') }}" class="border border-white/30 px-8 py-3 font-display font-medium uppercase tracking-widest transition hover:border-brand hover:text-brand">
        Contrate a banda
    </a>
</div>
