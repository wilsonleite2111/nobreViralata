# Memória do projeto — site da banda Soul True Noise (STN)

Resumo da sessão de 2026-10-10, em que o site foi planejado, construído do zero e passou por uma análise de segurança. Serve para retomar o trabalho sem reler a conversa. Atualize este arquivo quando uma decisão mudar.

## O que é

Site oficial da banda Soul True Noise (até 2026-10-16 era Nobre Vira Lata; o site foi construído com esse nome e depois renomeado) para divulgar shows, fotos, vídeos, notícias e produtos, inspirado no padrão do metallica.com (visual escuro, agenda em destaque, mídia e loja). Tudo é administrável em `/admin`.

O plano completo, com o que mudou durante a implementação e a lista do que falta para publicar, está em `E:\www\PLANO_NOBRE_VIRALATA.md` (fora deste repositório).

## Decisões do Wilson

- **Loja:** opção A, catálogo com compra externa (WhatsApp ou link). Checkout próprio (Mercado Pago + Melhor Envio) ficou como fase futura.
- **Idioma:** só português. Sem versão em inglês.
- **Notícias:** dentro do escopo. **Discografia:** fora do escopo.
- **Cores:** as da logo da Soul True Noise (2026-10-16) — fundo `#0e0d10`, fundo secundário `#1a191d`, prata do metal `#b6b7b0` como cor de destaque `brand` (escuro `#8f918b`), branco. Em 2026-10-16 o Wilson trocou o laranja do fogo (`#dd803a`) pelo prata no site inteiro. Ficam em `resources/css/app.css`, no `primary` do painel, no `theme-color` e na página de erro.
- **Nome exibido:** "Soul True Noise", vindo do `APP_NAME` do `.env`. Instagram: `@stn_soul_true_noise` (`config/site.php` e rodapé).
- **Nome do projeto:** STN = Soul True Noise. O `APP_NAME="stn"` do `.env.example` foi definido pelo Wilson e não deve ser revertido. Como o site exibe `config("app.name")` (títulos, rodapé, e-mails, dados estruturados) e os testes leem o `.env`, o `.env` local e o de produção usam `APP_NAME="Soul True Noise"`; com outro nome, 9 testes de SEO e layout falham.
- **Imagens:** as atuais estão em `E:\STN\Imagens` (`Logo.jpeg` → `public/images/logo.jpg`, quadrada; `Capa.jpg` → `public/images/capa.jpg`, reduzida a 1600 px; o favicon é a caveira recortada da logo). A pasta tem mais fotos da banda ainda não usadas. As da Nobre Vira Lata ficavam em `E:\NobreViraLata\Imagens oficiais`.
- **Domínio e hospedagem:** hospedagem compartilhada da Hostinger; domínio `www.bandanobreviralata.com.br`, registrado na própria Hostinger. Cloudflare fica para depois de o site estar no ar. **A rever:** o domínio ainda leva o nome antigo; o Wilson não decidiu se muda.
- **Ritmo de trabalho:** uma fase por vez; ao fim de cada fase o Wilson pede o commit e a fase seguinte. Só faça commit quando ele pedir.

## Estado atual

Fases 0 a 6 concluídas e commitadas na branch `main`, um commit por fase. Depois vieram a página de perfil no admin e as correções dos itens 1 a 5 da análise de segurança. A Fase 6 cobre só o que não depende de hospedagem.

O remoto é `https://github.com/wilsonleite2111/nobreViralata.git` (`origin`), e a `main` local está sincronizada com ele. Faça push só quando o Wilson pedir, assim como o commit.

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

Verificação na última rodada: 265 testes passando, Pint e Larastan (nível 6) sem erros. O Lighthouse mobile local com build de produção, medido na Fase 6, ficou entre 94 e 99 em desempenho e 100 em acessibilidade e SEO.

## Stack

- PHP 8.4, Laravel 13, Filament 5 (painel), Livewire, Alpine (vem com o Livewire), Tailwind 4, Vite.
- `spatie/laravel-medialibrary` com o plugin do Filament para todas as imagens enviadas.
- Pest 5, Larastan 3, Pint. Laravel Boost instalado (gera o bloco do `CLAUDE.md`, as skills em `.claude/skills` e o `.mcp.json`).
- Desenvolvimento local em MySQL 8.0 (banco `stn` em `127.0.0.1:3306`, usuário `root`) desde 2026-10-16; o SQLite antigo ficou para trás. Testes em SQLite em memória (`phpunit.xml`). Produção: MySQL/MariaDB da Hostinger.

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

## Deploy na Hostinger (em andamento, parado em 2026-10-13)

Nada foi feito no servidor ainda. O desenho combinado:

- Código por `git clone` via SSH em `domains/bandanobreviralata.com.br/app`; `public_html` vira link simbólico para `app/public`.
- `public/build` está fora do Git: rodar `npm run build` na máquina do Wilson e enviar por `scp`.
- Banco MySQL criado no hPanel; e-mail por SMTP da Hostinger com uma caixa do domínio. Em produção: `DB_CONNECTION=mysql` (funciona em MySQL e MariaDB) e `DB_HOST=localhost`.
- Fila por cron a cada minuto (`php artisan queue:work --stop-when-empty`), porque não há worker permanente.
- `APP_URL=https://www.bandanobreviralata.com.br`; o endereço sem `www` deve redirecionar para o com `www` no `.htaccess`, senão o `trustHosts()` o rejeita.
- As senhas não passam pelo chat: o `.env` de produção é montado no servidor e o Wilson preenche banco e e-mail.

Feito: chave SSH gerada em `C:\Users\Wilson\.ssh\id_ed25519` (sem senha, comentário `wilson-nobreviralata`, fingerprint `SHA256:YTYrSup9u+S2Doy0922c0dM5uIci4H+1g5gExq2Z08I`). A parte pública foi entregue ao Wilson para colar em hPanel → Avançado → Acesso SSH.

Aguardando o Wilson:

1. colar a chave pública no hPanel e informar IP, porta e usuário do SSH;
2. vincular o domínio ao plano;
3. PHP 8.4 com `intl` e `memory_limit` de 256 MB ou mais;
4. criar o banco MySQL e a caixa de e-mail;
5. conferir o SSL;
6. decidir se o repositório continua público (ver Pendências).

A conferir no primeiro acesso SSH: se o `php` da linha de comando é o 8.4, se há Composer 2 e se o cron aceita intervalo de 1 minuto.

Passos seguintes, depois disso: clone, `composer install --no-dev`, `.env`, `key:generate`, `migrate --force`, `storage:link`, envio do build, `optimize`, link do `public_html`, redirecionamento para `www`, cron da fila, usuário admin e teste de envio de e-mail e de upload de foto.

## Pendências

- **Publicação:** deploy em andamento (seção acima). Depois dele ainda faltam backup automático com teste de restauração e o Cloudflare. A lista original está na seção "Situação atual" do plano.
- **Repositório público:** em 2026-10-13 o `wilsonleite2111/nobreViralata` estava público no GitHub. Não há segredo nele (o `.env` é ignorado), mas este arquivo também fica visível. Se virar privado, o servidor precisa de uma deploy key para o clone.
- **Conteúdo real:** o banco local está vazio, exceto por um usuário admin criado para o Wilson. A senha gerada na sessão deve ser trocada antes de publicar.
- **Segurança, itens em aberto da análise de 2026-10-10** (os itens 1 a 5 foram corrigidos; estes não foram pedidos):
  - item 6: todo usuário é admin total e não há segundo fator;
  - item 7: o texto rico aceita `style` e imagens externas;
  - item 8: a confirmação da newsletter é por GET;
  - item 9: fotos de álbuns em rascunho abrem por link direto;
  - os formulários não têm CAPTCHA, e a política de conteúdo não restringe scripts (Alpine, Livewire e Filament usam código inline).
- **Segurança em produção:** `APP_DEBUG=false`, `APP_URL` com o domínio real (o `trustHosts()` depende dele), `TRUSTED_PROXIES` com os endereços do proxy ou do Cloudflare, `SESSION_SECURE_COOKIE=true`, docroot em `public/`, `composer install --no-dev`.
- **Foto de abertura:** falta uma foto horizontal grande, sem a faixa da logo, para o hero em tela cheia.
- **Política de privacidade:** o texto é um rascunho e precisa de revisão.
- **Ideias mencionadas e não pedidas:** preço e SKU por variação, PhotoSwipe (zoom por pinça), imagens no corpo das notícias, envio da newsletter pelo site, mapa embutido nos shows.
