# Memória do projeto — site da banda Nobre Vira Lata

Resumo da sessão de 2026-10-10, em que o site foi planejado e construído do zero. Serve para retomar o trabalho sem reler a conversa. Atualize este arquivo quando uma decisão mudar.

## O que é

Site oficial da banda Nobre Vira Lata para divulgar shows, fotos, vídeos, notícias e produtos, inspirado no padrão do metallica.com (visual escuro, agenda em destaque, mídia e loja). Tudo é administrável em `/admin`.

O plano completo, com o que mudou durante a implementação e a lista do que falta para publicar, está em `E:\www\PLANO_NOBRE_VIRALATA.md` (fora deste repositório).

## Decisões do Wilson

- **Loja:** opção A, catálogo com compra externa (WhatsApp ou link). Checkout próprio (Mercado Pago + Melhor Envio) ficou como fase futura.
- **Idioma:** só português. Sem versão em inglês.
- **Notícias:** dentro do escopo. **Discografia:** fora do escopo.
- **Cores:** as da logo oficial — preto `#1f1a17`, laranja `#e67817`, branco.
- **Nome exibido:** "Nobre Vira Lata" (três palavras, como na logo), embora o pedido original escreva "Viralata". Instagram: `@bandanobreviralata`.
- **Imagens oficiais:** `E:\NobreViraLata\Imagens oficiais` (duas logos e duas fotos). A mesma pasta tem áudios, letras e repertório antigos, ainda não usados no site.
- **Domínio e hospedagem:** ainda não contratados.
- **Ritmo de trabalho:** uma fase por vez; ao fim de cada fase o Wilson pede o commit e a fase seguinte. Só faça commit quando ele pedir.

## Estado atual

Fases 0 a 6 concluídas e commitadas na branch `main` (um commit por fase, último: `61f4ab3`). Sem remoto no GitHub. A Fase 6 cobre só o que não depende de hospedagem.

| Área | Público | Admin |
|---|---|---|
| Shows | `/shows`, `/shows/anteriores`, `/shows/{slug}` | `/admin/shows` |
| Fotos | `/fotos`, `/fotos/{slug}` | `/admin/photo-albums` |
| Vídeos | `/videos` | `/admin/videos` |
| Notícias | `/noticias`, `/noticias/{slug}` | `/admin/posts` |
| Banda | `/banda` | `/admin/members`, `/admin/band` |
| Loja | `/loja`, `/loja/{slug}` | `/admin/products`, `/admin/product-categories`, `/admin/store` |
| Home, contato, newsletter | `/`, `/contato`, `/newsletter/...` | `/admin/site`, `/admin/contact-messages`, `/admin/subscribers` |
| SEO e institucional | `/sitemap.xml`, `/robots.txt`, `/privacidade` | — |

Verificação na última rodada: 242 testes passando, Pint e Larastan (nível 6) sem erros, Lighthouse mobile local com build de produção entre 94 e 99 em desempenho e 100 em acessibilidade e SEO.

## Stack

- PHP 8.4, Laravel 13, Filament 5 (painel), Livewire, Alpine (vem com o Livewire), Tailwind 4, Vite.
- `spatie/laravel-medialibrary` com o plugin do Filament para todas as imagens enviadas.
- Pest 5, Larastan 3, Pint. Laravel Boost instalado (gera o bloco do `CLAUDE.md`, as skills em `.claude/skills` e o `.mcp.json`).
- SQLite em desenvolvimento; MySQL previsto para produção.

## Como o código está organizado

- **Enums** ficam em `app/` (`ShowStatus`, `VideoCategory`), onde o `make:enum` os criou.
- **Publicação:** shows, álbuns e vídeos usam `is_published`; produtos usam `is_active`; notícias usam `is_published` mais `published_at` (data futura agenda a notícia). Cada model tem o escopo correspondente (`published()` ou `active()`), e item não publicado devolve 404.
- **Shows:** um show continua em "próximos" até o fim do dia em que acontece.
- **Imagens:** toda coleção de mídia usa `->useDisk('public')` no model e `->visibility('public')` no campo de upload. Sem isso, o Filament grava no disco privado e a foto não aparece no site. As conversões geram WebP.
- **Vídeos:** guardam só o ID do YouTube (`Video::extractYoutubeId` aceita link ou ID). O player carrega apenas depois do clique.
- **Texto rico** (história da banda e notícias): sempre exibido pelo componente `<x-rich-text>`, que limpa o HTML na saída.
- **Configurações do site:** tabela `settings` de chave e valor, com as chaves como constantes em `App\Models\Setting` e três páginas no admin (`ManageBand`, `ManageStore`, `ManageSite`).
- **Loja:** preço em centavos (`price_cents`). Produto com variações usa o estoque das variações; sem variações, usa o estoque próprio. O botão de compra vem de `Product::purchaseUrl()`: link externo se houver, senão WhatsApp com produto e variação na mensagem.
- **Formulários públicos** (contato e newsletter): Form Request com mensagens em português, campo oculto `website` como honeypot e o limitador `throttle:public-forms` (5 por minuto e 30 por dia, por IP). A newsletter manda no máximo 3 e-mails de confirmação por endereço por dia.
- **Segurança:** o middleware `SecurityHeaders` roda em toda resposta; `trustHosts()` rejeita hosts diferentes do `APP_URL` fora do ambiente local; proxies confiáveis vêm de `TRUSTED_PROXIES` (`config/trustedproxy.php`), vazio por padrão.
- **Páginas de erro:** o modelo independente é `errors/standalone.blade.php`. Não crie `errors/minimal.blade.php` nem `errors/layout.blade.php`: esses nomes são usados pelo próprio Laravel e sobrepô-los transforma 401/402/403 em erro 500.
- **Newsletter:** confirmação por e-mail (double opt-in) e saída com etapa de confirmação. O site só coleta a lista; não há envio de newsletter.
- **SEO:** o layout `<x-layouts.site>` recebe `title`, `description`, `image`, `type` e `noindex`; dados estruturados passam por `<x-structured-data>`.
- **Perfil no admin:** `/admin/profile` permite ao usuário trocar nome, e-mail e senha (a troca de senha exige a senha atual).
- **Layout:** os itens do menu estão no próprio `resources/views/components/layouts/site.blade.php`; o link do Instagram está em `config/site.php`.

## Armadilhas já encontradas

- **Fila:** miniaturas das fotos e e-mails saem pela fila (`QUEUE_CONNECTION=database`). O `composer run dev` roda a fila; só com `php artisan serve` nada disso é processado.
- **E-mail:** o `.env` local usa `MAIL_MAILER=log`, então os e-mails vão para `storage/logs/laravel.log`.
- **`APP_URL`:** os endereços das fotos saem do `APP_URL` (`http://localhost:8000`). Em outra porta as fotos não aparecem.
- **`public/hot`:** com o servidor de desenvolvimento do Vite rodando, qualquer servidor PHP do projeto usa os assets de desenvolvimento. Para medir desempenho é preciso servir com o build de produção.
- **Memória:** o PHP tem 128 MB; fotos muito grandes podem falhar na conversão. Os testes rodam com 512 MB (`phpunit.xml`).
- **`intl`:** o Filament exige a extensão; foi habilitada no `php.ini` global (`E:\work\php\8.4\php.ini`).
- **Validação em inglês no admin:** o Laravel não traz traduções pt_BR; os formulários públicos têm mensagens próprias, mas o painel mostra frases como "The título field is required." Instalar `laravel-lang` resolveria e não foi autorizado ainda.
- **Dados de exemplo:** os seeders gravam arquivos em `storage/app/public`. Para conferências visuais, use um banco SQLite temporário e apague esses arquivos depois, conferindo antes se o banco real não tem mídia cadastrada.
- **Testes do Filament:** validação de `RichEditor` vazio não expõe a regra `required` (teste só a chave); conteúdo de modal não aparece no HTML (teste o estado do componente); repetidores precisam de `Repeater::fake()`.

## Pendências

- **Publicação:** tudo que depende de hospedagem (configuração de produção, deploy, HTTPS, worker de fila, SMTP, backup). A lista está na seção "Situação atual" do plano.
- **Conteúdo real:** o banco local está vazio, exceto por um usuário admin criado para o Wilson. A senha gerada na sessão deve ser trocada antes de publicar.
- **Segurança, itens em aberto da análise de 2026-10-10:** todo usuário é admin total e não há segundo fator; o texto rico aceita `style` e imagens externas; a confirmação da newsletter é por GET; fotos de álbuns em rascunho abrem por link direto; os formulários não têm CAPTCHA. Em produção, definir `TRUSTED_PROXIES` e `SESSION_SECURE_COOKIE=true`.
- **Foto de abertura:** falta uma foto horizontal grande, sem a faixa da logo, para o hero em tela cheia.
- **Política de privacidade:** o texto é um rascunho e precisa de revisão.
- **Ideias mencionadas e não pedidas:** preço e SKU por variação, PhotoSwipe (zoom por pinça), imagens no corpo das notícias, envio da newsletter pelo site, mapa embutido nos shows.
