<x-layouts.site title="Cancelar inscrição" :noindex="true">
    <section class="mx-auto max-w-2xl px-4 py-24 text-center lg:px-8 lg:py-32">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Cancelar inscrição</h1>
        <p class="mt-6 text-lg text-white/70">Quer mesmo parar de receber as novidades da banda em <strong class="text-white">{{ $subscriber->email }}</strong>?</p>

        <form method="POST" action="{{ route('newsletter.destroy', $subscriber) }}" class="mt-10">
            @csrf
            @method('DELETE')

            <button type="submit" class="bg-brand px-8 py-3 font-display font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                Sim, cancelar inscrição
            </button>
        </form>
    </section>
</x-layouts.site>
