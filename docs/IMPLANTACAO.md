# Implantação na Hostinger

Checklist para publicar uma cópia de demonstração ou uma versão de produção da Malu Store.

## Antes de começar

- Configure o `.env` com o banco MySQL correto, URL do site, `APP_ENV=production` e `APP_DEBUG=false`.
- Instale dependências PHP com `composer install --no-dev --optimize-autoloader`.
- Envie os arquivos compilados de `public/build` ou execute `npm run build` antes do deploy.
- Envie também `storage/app/public/products` para preservar as fotos do catálogo.
- Configure o domínio da hospedagem para servir a pasta `public`, nunca a raiz inteira do projeto.

### Base recomendada para produção

O `.env.example` já traz os padrões locais do Malu Store: português do Brasil, MySQL, disco público para uploads e logs diários. No servidor, confirme pelo menos estes valores e nunca versione senhas ou chaves:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR

DB_CONNECTION=mysql
FILESYSTEM_DISK=public
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

O comando cria a estrutura e insere categorias, produtos, imagens cadastradas, contas e pedidos de demonstração. As imagens continuam dependendo dos arquivos enviados para `storage/app/public/products`.

## Arquivos públicos e SEO

- Mantenha `public/build` junto do deploy (ou execute `npm run build` antes de enviar os arquivos).
- O link `public/storage` é criado pelo `storage:link`; ele deve apontar para `storage/app/public`. Se o projeto for movido de pasta ou de servidor, recrie esse link com esse comando.
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

## Fila e webhooks

Como o webhook do Asaas e a geração de etiquetas são colocados na fila `database`, mantenha um worker ativo:

```bash
php artisan queue:work --tries=3 --timeout=120 --sleep=3
```

Configure esse processo pelo recurso de processos/cron da hospedagem de acordo com o plano contratado. O worker usa até três tentativas e 120 segundos por Job. Sem worker, webhooks e etiquetas entram na tabela `jobs`, mas não são processados. Consulte e repita trabalhos que falharam somente depois de investigar:

```bash
php artisan queue:failed
php artisan queue:retry all
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
```

`SESSION_SAME_SITE` já é lida nativamente por `config/session.php`; a variável não precisa existir em outro arquivo de configuração. `lax` preserva os fluxos normais da loja e reduz o risco de requisições cross-site. Os cabeçalhos HTTP de proteção são aplicados automaticamente pela aplicação; HSTS só é enviado quando a requisição já usa HTTPS.

### Assinaturas dos webhooks

- Asaas: configure um token longo no painel do Asaas e use o mesmo valor em `ASAAS_PRODUCTION_WEBHOOK_TOKEN`. A aplicação valida o cabeçalho `asaas-access-token` antes de enfileirar o evento.
- Melhor Envio: copie o *secret* do aplicativo para `MELHOR_ENVIO_WEBHOOK_SECRET`. A aplicação valida o cabeçalho `X-ME-Signature` via HMAC-SHA256 no corpo original da requisição.

Não use chaves de API como tokens de webhook. Depois de alterar variáveis, execute `php artisan optimize:clear` e `php artisan config:cache`.

### Cartão de crédito

O checkout não salva nem registra números de cartão ou CVV. A loja exige HTTPS para esse fluxo. Para reduzir ainda mais o escopo de dados sensíveis, habilite a tokenização de cartão na conta Asaas e planeje migrar o checkout para `creditCardToken`; essa habilitação depende de aprovação do Asaas em produção.

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
MELHOR_ENVIO_PRODUCTION_TOKEN=
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
