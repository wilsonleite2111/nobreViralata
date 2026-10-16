<x-layouts.site title="Política de privacidade" description="Como o site da banda Soul True Noise trata os dados pessoais de quem o visita.">
    <article class="mx-auto max-w-3xl px-4 py-12 lg:px-8 lg:py-20">
        <h1 class="font-display text-4xl font-bold uppercase tracking-wide lg:text-6xl">Política de privacidade</h1>

        <div class="rich-text mt-8">
            <p>Esta página explica, de forma direta, quais dados pessoais o site da banda {{ config('app.name') }} recebe, para que são usados e como você pode pedir que sejam apagados, conforme a Lei Geral de Proteção de Dados (Lei nº 13.709/2018).</p>

            <h2>Quais dados recebemos</h2>
            <ul>
                <li><strong>Newsletter:</strong> o e-mail que você informa ao se inscrever, e as datas em que confirmou ou cancelou a inscrição.</li>
                <li><strong>Formulário de contato:</strong> nome, e-mail, assunto e a mensagem que você escreve.</li>
            </ul>
            <p>O site não pede cadastro, não tem login para visitantes e não processa pagamentos. As compras são combinadas pelo WhatsApp ou em lojas externas, que têm suas próprias políticas.</p>

            <h2>Para que usamos</h2>
            <ul>
                <li>O e-mail da newsletter serve apenas para enviar novidades da banda, e só depois que você confirma a inscrição pelo link recebido.</li>
                <li>Os dados do formulário de contato servem apenas para responder à sua mensagem.</li>
            </ul>
            <p>Não vendemos nem compartilhamos esses dados com terceiros para publicidade.</p>

            <h2>Cookies</h2>
            <p>O site usa somente cookies necessários ao seu funcionamento, como o que protege os formulários contra envios forjados. Não usamos cookies de publicidade nem de rastreamento.</p>
            <p>Os vídeos são do YouTube e só são carregados quando você clica para assistir. A partir desse clique, o YouTube pode usar os próprios cookies, conforme a política dele.</p>

            <h2>Por quanto tempo guardamos</h2>
            <p>O e-mail da newsletter fica guardado enquanto a inscrição estiver ativa. As mensagens de contato ficam guardadas pelo tempo necessário para responder e acompanhar o assunto.</p>

            <h2>Seus direitos</h2>
            <p>Você pode sair da newsletter a qualquer momento pelo link de cancelamento presente nos e-mails. Para consultar, corrigir ou apagar seus dados, escreva pelo <a href="{{ route('contact') }}">formulário de contato</a>.</p>
        </div>
    </article>
</x-layouts.site>
