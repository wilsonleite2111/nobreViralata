<x-mail::message>
# Falta só confirmar

Recebemos um pedido para inscrever este e-mail na newsletter da {{ config('app.name') }}. Clique no botão para confirmar e receber novidades de shows, vídeos e lançamentos.

<x-mail::button :url="$confirmUrl">
Confirmar inscrição
</x-mail::button>

Se não foi você, é só ignorar este e-mail: sem a confirmação, nada será enviado.

Para sair da lista a qualquer momento: [cancelar inscrição]({{ $unsubscribeUrl }}).

{{ config('app.name') }}
</x-mail::message>
