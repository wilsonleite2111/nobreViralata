<x-mail::message>
# Nova mensagem pelo site

**Nome:** {{ $contactMessage->name }}<br>
**E-mail:** {{ $contactMessage->email }}<br>
**Assunto:** {{ $contactMessage->subject }}

<x-mail::panel>
{!! nl2br(e($contactMessage->message)) !!}
</x-mail::panel>

Responder a este e-mail envia a resposta direto para {{ $contactMessage->name }}.
</x-mail::message>
