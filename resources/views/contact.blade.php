@php
    $fieldClasses = 'mt-2 w-full border border-white/30 bg-ink-soft px-4 py-3 text-white placeholder:text-white/40 focus:border-brand focus:outline-none';
    $labelClasses = 'font-display text-sm uppercase tracking-widest text-white/70';
@endphp

<x-layouts.site title="Contato" description="Fale com a banda Soul True Noise: contratação de shows, imprensa e parcerias.">
    <section class="mx-auto max-w-2xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Contato</h1>
        <p class="mt-4 text-lg text-white/70">Quer contratar a banda, falar de parceria ou só mandar um alô? Escreva pra gente.</p>

        @if (session('contact_status'))
            <p class="mt-8 border border-brand px-6 py-4 text-lg" role="status">{{ session('contact_status') }}</p>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-6">
            @csrf

            {{-- Honeypot: hidden from people, filled in by bots. --}}
            <div class="hidden" aria-hidden="true">
                <label for="website">Não preencha este campo</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label for="name" class="{{ $labelClasses }}">Nome</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="{{ $fieldClasses }}" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                @error('name')
                    <p id="name-error" class="mt-2 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="{{ $labelClasses }}">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="{{ $fieldClasses }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                @error('email')
                    <p id="email-error" class="mt-2 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject" class="{{ $labelClasses }}">Assunto</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="255" class="{{ $fieldClasses }}" @error('subject') aria-invalid="true" aria-describedby="subject-error" @enderror>
                @error('subject')
                    <p id="subject-error" class="mt-2 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="{{ $labelClasses }}">Mensagem</label>
                <textarea id="message" name="message" rows="6" required maxlength="5000" class="{{ $fieldClasses }}" @error('message') aria-invalid="true" aria-describedby="message-error" @enderror>{{ old('message') }}</textarea>
                @error('message')
                    <p id="message-error" class="mt-2 text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-brand px-10 py-4 font-display text-lg font-medium uppercase tracking-widest text-ink transition hover:bg-brand-dark">
                Enviar mensagem
            </button>

            <p class="text-sm text-white/60">
                Usamos seus dados só para responder à sua mensagem. Veja a <a href="{{ route('privacy') }}" class="underline hover:text-brand">política de privacidade</a>.
            </p>
        </form>
    </section>
</x-layouts.site>
