# Implantação na Hostinger

Checklist para publicar uma cópia de demonstração ou uma versão de produção da Malu Store.

> A aplicação requer PHP 8.4 ou 8.5 e Laravel 13. Confirme a versão do PHP usada pelo servidor web e pelo worker de filas antes do deploy.

## Antes de começar

- Configure o `.env` com o banco MySQL correto, URL do site, `APP_ENV=production` e `APP_DEBUG=false`.
- Instale dependências PHP com `composer install --no-dev --optimize-autoloader`.
- Execute `npm ci && npm run build` no artefato de cada release antes do deploy.
- Confirme o acesso ao Redis e ao bucket R2 antes de ativar a release. Imagens
  locais legadas podem ser copiadas com `storage:migrate-products`.
- Configure o domínio da hospedagem para servir a pasta `public`, nunca a raiz inteira do projeto.

### Base recomendada para produção

O `.env.example` traz padrões locais. Produção usa Redis e R2; confirme pelo
menos estes valores no servidor e nunca versione senhas ou chaves:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR

DB_CONNECTION=mysql
CACHE_STORE=failover
SESSION_DRIVER=database
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_URL=
REDIS_PREFIX=malu-store-production-
REDIS_CACHE_DB=1
REDIS_CONNECT_TIMEOUT=3
REDIS_READ_TIMEOUT=10
REDIS_MAX_RETRIES=2
REDIS_BACKOFF_BASE=100
REDIS_BACKOFF_CAP=1000

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=malu-store-products-production
AWS_ENDPOINT=https://ACCOUNT_ID.r2.cloudflarestorage.com
AWS_URL=https://assets.malu-store.com
AWS_USE_PATH_STYLE_ENDPOINT=false

SESSION_SECURE_COOKIE=true
LOG_STACK=daily
LOG_LEVEL=info
LOG_DAILY_DAYS=14
```

`LOG_LEVEL=info` preserva os registros operacionais de checkout e pagamentos. Caso o volume de logs fique alto, altere para `warning`, sabendo que os registros informativos deixam de ser gravados.

## Banco de demonstração

Somente em um banco que pode ser apagado, rode por SSH na pasta que contém `artisan`:

```bash
php artisan migrate:fresh --seed --force
php artisan storage:link
php artisan optimize:clear
```

O comando cria a estrutura e insere categorias, produtos, imagens cadastradas,
contas e pedidos de demonstração. Localmente, as imagens dependem dos arquivos em
`storage/app/public/products`; nos ambientes hospedados, dependem dos objetos no
bucket R2.

## Assets públicos e SEO

- `public/build` é sempre gerado a partir de `resources`, `package-lock.json` e `vite.config.js`; ele não é versionado.
- A CI compila os assets em todos os commits. O deploy deve executar `npm ci && npm run build` em uma pasta de release limpa, antes da troca atômica do diretório público.
- O Vite limpa `public/build` antes de cada compilação, evitando arquivos com hash obsoleto. Não copie esse diretório entre releases.
- O link `public/storage` atende o disco local e arquivos legados. Imagens novas
  usam o disco definido por `FILESYSTEM_DISK`; com `s3`, a URL vem de `AWS_URL`.
- O ícone público principal é `public/favicon.svg`; `public/favicon.ico` é o fallback de compatibilidade para navegadores e atalhos antigos. Ambos são carregados pelos layouts público, de pagamento e administrativo.
- Não envie `public/hot` para produção. Esse arquivo é criado somente pelo Vite em desenvolvimento e faz o Laravel procurar os assets no servidor local.
- Após cadastrar ou alterar o catálogo em produção, gere o sitemap com `php artisan sitemap:generate`. O comando usa o `APP_URL` do ambiente, por isso essa variável deve conter o domínio HTTPS definitivo antes de executá-lo.

## Atualização de produção com dados reais

Não use `migrate:fresh`. Execute apenas migrations novas:

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan sitemap:generate
```

## Pipeline protegido de produção

O workflow **Deploy de produção** é iniciado manualmente no GitHub e aceita somente um commit contido em `main`. Ele repete testes, análise estática, auditorias e build; depois cria um arquivo com checksum, guarda o artefato por 30 dias e usa o Environment `production` para ativá-lo.

Secrets exigidos no Environment `production`:

- `PRODUCTION_SSH_HOST`
- `PRODUCTION_SSH_PORT`
- `PRODUCTION_SSH_USER`
- `PRODUCTION_SSH_PRIVATE_KEY`
- `PRODUCTION_SSH_KNOWN_HOSTS`

Antes da troca, o servidor copia `.env` e `storage` da versão ativa para a nova release. Eles permanecem dentro de `public_html`, como exige o `open_basedir` do PHP web da Hostinger. Depois, o pipeline aguarda até 30 segundos pela atualização do servidor web. A release só é confirmada quando `/up` responde com sucesso e o arquivo `RELEASE_COMMIT` corresponde ao commit solicitado. Em caso de falha, o script restaura automaticamente a versão anterior. Nunca coloque senha, chave privada ou conteúdo do `.env` no workflow ou no repositório.

## Fila e webhooks

Pagamentos, webhooks e operações de envio usam filas Redis nos ambientes
hospedados. Como a hospedagem não mantém Horizon continuamente, configure no
agendador workers curtos e separados por prioridade:

```bash
php artisan queue:work redis --queue=critical --stop-when-empty --tries=3 --timeout=120
php artisan queue:work redis --queue=default --stop-when-empty --tries=3 --timeout=120
```

Configure cada comando com um `flock` próprio e execute também
`artisan schedule:run` a cada minuto. Sem worker, os jobs permanecem no Redis sem
ser processados. Consulte e repita trabalhos que falharam somente depois de
investigar:

```bash
php artisan queue:failed
php artisan queue:retry ID
```

## Segurança de produção

No `.env` da hospedagem, use HTTPS e mantenha estas configurações:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SECURITY_HSTS=true
ADMIN_TWO_FACTOR_ENABLED=true
```

`SESSION_SAME_SITE` já é lida nativamente por `config/session.php`; a variável não precisa existir em outro arquivo de configuração. `lax` preserva os fluxos normais da loja e reduz o risco de requisições cross-site. Os cabeçalhos HTTP de proteção são aplicados automaticamente pela aplicação; HSTS só é enviado quando a requisição já usa HTTPS.

O middleware `SecurityHeaders` aplica uma CSP restritiva com nonce único por
resposta, bloqueia objetos, frames e scripts inline sem nonce, limita imagens aos
domínios autorizados e remove `X-Powered-By`. O `.htaccess` também remove esse
cabeçalho no Apache. O front-end usa a distribuição CSP do Alpine.js, portanto
`script-src` não permite `unsafe-eval`; a diretiva
`style-src-attr 'unsafe-inline'` permanece somente para cores dinâmicas de produtos. Ao adicionar CDN,
fonte, script ou integração no navegador, ajuste a CSP pelo menor escopo possível
e execute os testes de navegador nos dois temas.

Validação sem expor cookies ou conteúdo:

```bash
curl -sSI https://malu-store.com/ | grep -Ei \
  '^(content-security-policy|x-powered-by|strict-transport-security|x-content-type-options|x-frame-options):'
```

O resultado deve conter CSP, HSTS e os demais cabeçalhos de proteção, mas não
deve conter `X-Powered-By`.

### Assinaturas dos webhooks

- Asaas: configure um token longo no painel do Asaas e use o mesmo valor em `ASAAS_PRODUCTION_WEBHOOK_TOKEN`. A aplicação valida o cabeçalho `asaas-access-token` antes de enfileirar o evento.
- Melhor Envio: use `MELHOR_ENVIO_SANDBOX_WEBHOOK_SECRET` e
  `MELHOR_ENVIO_PRODUCTION_WEBHOOK_SECRET`. A aplicação seleciona o secret pelo
  `MELHOR_ENVIO_ENV` e valida `X-ME-Signature` via HMAC-SHA256 no corpo original.

Os dois endpoints limitam o corpo a 256 KiB, aceitam no máximo 120 requisições por
minuto e ignoram por 24 horas uma repetição byte a byte do mesmo evento. O
processamento continua assíncrono na fila `critical`.

Não use chaves de API como tokens de webhook. Depois de alterar variáveis, execute `php artisan optimize:clear` e `php artisan config:cache`.

### Acesso administrativo em duas etapas

Em produção, mantenha `ADMIN_TWO_FACTOR_ENABLED=true`. Depois de validar e-mail e
senha, o painel envia um código de seis dígitos ao endereço do administrador. O
código dura 10 minutos, permite no máximo cinco tentativas e fica armazenado
somente como hash na sessão. Uma conta desativada perde acesso imediatamente,
mesmo que já possua uma sessão aberta. Antes de ativar, valide o SMTP pelo
procedimento de teste de e-mail desta documentação.

### Cartão de crédito

O cartão usa o Asaas Checkout hospedado. Número, validade e CVV são informados exclusivamente no domínio HTTPS do Asaas; o navegador não os envia à Malu Store. A criação do Checkout apenas inicia a jornada: somente o evento `CHECKOUT_PAID`, autenticado e processado pela fila, confirma o pedido.

No webhook do Asaas, habilite também `CHECKOUT_CREATED`, `CHECKOUT_PAID`, `CHECKOUT_CANCELED` e `CHECKOUT_EXPIRED`. Mantenha o mesmo token longo configurado em `ASAAS_PRODUCTION_WEBHOOK_TOKEN`.

## Ambientes das APIs: sandbox e produção

As integrações não usam mais uma URL que precisa ser comentada e trocada manualmente. Cada serviço possui um seletor de ambiente e credenciais próprias. Defina os dois seletores como `sandbox` durante os testes e, somente após homologar todos os fluxos, altere ambos para `production`.

```env
ASAAS_ENV=sandbox
MELHOR_ENVIO_ENV=sandbox
MELHOR_ENVIO_ORIGIN_ZIP=01001000
```

Preencha as chaves do ambiente correspondente no `.env`, sem versioná-las:

```env
ASAAS_SANDBOX_API_KEY=
ASAAS_SANDBOX_WEBHOOK_TOKEN=
ASAAS_PRODUCTION_API_KEY=
ASAAS_PRODUCTION_WEBHOOK_TOKEN=

MELHOR_ENVIO_SANDBOX_TOKEN=
MELHOR_ENVIO_SANDBOX_WEBHOOK_SECRET=
MELHOR_ENVIO_PRODUCTION_TOKEN=
MELHOR_ENVIO_PRODUCTION_WEBHOOK_SECRET=
MELHOR_ENVIO_ORIGIN_ZIP=
MELHOR_ENVIO_SENDER_NAME=
MELHOR_ENVIO_SENDER_PHONE=
MELHOR_ENVIO_SENDER_EMAIL=
MELHOR_ENVIO_SENDER_DOCUMENT=
MELHOR_ENVIO_SENDER_ADDRESS=
MELHOR_ENVIO_SENDER_NUMBER=
MELHOR_ENVIO_SENDER_DISTRICT=
MELHOR_ENVIO_SENDER_CITY=
MELHOR_ENVIO_SENDER_STATE=SP
MELHOR_ENVIO_USER_AGENT="Malu Store (suporte@sua-loja.com)"
```

Os dados `MELHOR_ENVIO_SENDER_*` são exigidos somente para emitir etiquetas e identificam o remetente real da loja. Preencha-os exclusivamente no `.env` do servidor; não use dados de exemplo em produção.

As URLs padrão já são fornecidas pelo `config/services.php`: Asaas usa `https://api-sandbox.asaas.com/v3` no sandbox e `https://api.asaas.com/v3` em produção; Melhor Envio usa o endpoint `/api/v2/me/` correspondente ao ambiente. Só altere as variáveis `*_BASE_URL` se a documentação oficial do fornecedor mudar.

Antes de mudar para produção, configure os webhooks e os tokens de produção nos dois fornecedores. Depois da alteração, recrie o cache de configuração:

```bash
php artisan optimize:clear
php artisan config:cache
```

> Não misture chave sandbox com URL de produção, nem chave de produção com URL sandbox. O Asaas mantém credenciais e dados totalmente separados por ambiente; o Melhor Envio também utiliza tokens distintos.

## Alertas por e-mail (opcional)

O projeto já registra falhas críticas de checkout, pagamentos, frete e webhook em `storage/logs/laravel.log`. O envio de alertas por e-mail não é ativado automaticamente: essa decisão depende do e-mail remetente e do destinatário aprovados para produção.

Para usar uma conta Gmail como remetente, crie uma **senha de app** na conta Google com a verificação em duas etapas ativada. No `.env` do servidor, configure os valores abaixo com dados reais. Nunca versione ou compartilhe a senha de app.

```env
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alertas@sua-loja.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_FROM_ADDRESS=alertas@sua-loja.com
MAIL_FROM_NAME="Malu Store"
ALERT_EMAIL=responsavel@sua-loja.com
```

Após salvar o `.env`, limpe e recrie o cache de configuração:

```bash
php artisan optimize:clear
php artisan config:cache
```

`MAIL_USERNAME` é a conta que envia os e-mails; `ALERT_EMAIL` é quem os recebe e pode ser o mesmo endereço. Para Gmail na porta `587`, use `MAIL_SCHEME=smtp`: o TLS é negociado automaticamente. Caso a conta remetente seja uma caixa criada na Hostinger, mantenha o host SMTP fornecido pela própria Hostinger em vez de `smtp.gmail.com`.

Com a variável `ALERT_EMAIL` configurada, o sistema envia alertas para falha definitiva do webhook do Asaas e falhas técnicas ao criar cobranças ou calcular frete. O conteúdo do e-mail usa somente contexto técnico mínimo; não inclui senha, dados de cartão, payload do webhook, QR Code ou dados pessoais completos.

Teste o canal depois de configurar o SMTP:

```bash
php artisan alerts:test
```

O comando solicita um e-mail de teste sem exigir uma falha real. Se o e-mail não chegar, consulte `storage/logs/laravel.log` para verificar um eventual erro de SMTP.

## Testes de navegador

Os fluxos críticos usam Playwright e um banco SQLite descartável. Nenhum teste acessa gateways reais: pagamento e frete não são submetidos, e todas as contas são fictícias. Para executar localmente, instale o Chromium uma vez e rode:

```bash
npm ci
npm run build
npx playwright install chromium
npm run test:e2e
```

A CI executa cadastro, login, inclusão na sacola, acesso ao checkout e consulta administrativa de pedidos. Em falhas, relatório, captura de tela, vídeo e trace ficam disponíveis por sete dias no artefato `browser-diagnostics`.

## Monitoramento externo

O workflow **Monitor de produção** consulta `/up` e `/produtos` a cada 15 minutos, com três novas tentativas para falhas transitórias. Se algum endpoint continuar indisponível, ele abre uma issue de prioridade alta no GitHub. Enquanto a primeira issue estiver aberta, novas execuções falhas não criam duplicatas.

Também é possível executar o monitor manualmente pela aba Actions. Depois de resolver um incidente, valide os dois endpoints, registre a causa e a correção na issue e só então feche-a. A próxima indisponibilidade poderá criar uma nova ocorrência.

`/up` confirma que o Laravel iniciou. `/health` é a verificação de prontidão e
testa MySQL, fila/Redis e escrita/exclusão no disco padrão (R2 em produção). Use
`/health` em diagnóstico e antes/depois de testes de carga; ele pode responder
`503` mesmo quando `/up` ainda responde `200`.

## Backup e recuperação

O backup diário reúne um dump consistente do MySQL e os arquivos locais legados
em `storage/app/public`, criptografa o pacote com AES-256 e grava um checksum
SHA-256. A retenção padrão é de 14 cópias. Credenciais, nome do banco e chave de
criptografia ficam fora de `public_html`, com permissão somente para o usuário da
hospedagem (`~/.malu-store-backup.cnf`, `~/.malu-store-backup-database` e
`~/.malu-store-backup.key`).

Esse script não exporta objetos do R2. Configure no Cloudflare um mecanismo de
retenção e recuperação compatível com o plano contratado e teste separadamente a
recuperação das imagens.

O workflow **Backup de produção** executa o script diariamente às 03:15 no horário de Brasília. Uma falha abre uma issue de produção sem duplicar incidentes ainda abertos. Como alternativa, o mesmo comando pode ser configurado no Agendador do hPanel:

```bash
bash /home/USUARIO/domains/malu-store.com/public_html/scripts/backup/create-backup.sh
```

O objetivo de recuperação é **RPO de até 24 horas** e **RTO de até 2 horas**. A restauração sempre deve começar em um banco separado; nunca aponte o comando diretamente para o banco de produção:

```bash
BACKUP_MYSQL_CONFIG=/caminho/credenciais-de-teste.cnf \
BACKUP_ENCRYPTION_KEY_FILE=/caminho/chave \
RESTORE_UPLOADS_DESTINATION=/caminho/uploads-restaurados \
bash scripts/backup/restore-backup.sh /caminho/malu-store-DATA.tar.gz.enc banco_restauracao
```

Confira pedidos, produtos, usuários, imagens e contagens antes de promover os dados restaurados. O workflow **Teste de restauração de backup** executa mensalmente o ciclo completo em bancos descartáveis e também pode ser iniciado manualmente. A chave de produção deve ter uma cópia em um cofre externo à hospedagem; sem ela, o backup criptografado não pode ser recuperado.
