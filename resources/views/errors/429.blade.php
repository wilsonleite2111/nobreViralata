@include('errors.standalone', [
    'code' => 429,
    'title' => 'Muitas tentativas',
    'message' => 'Você fez muitos envios em pouco tempo. Aguarde um minuto e tente de novo.',
])
