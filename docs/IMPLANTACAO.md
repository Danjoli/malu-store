# Implantação na Hostinger

Checklist para publicar uma cópia de demonstração ou uma versão de produção da Malu Store.

## Antes de começar

- Configure o `.env` com o banco MySQL correto, URL do site, `APP_ENV=production` e `APP_DEBUG=false`.
- Instale dependências PHP com `composer install --no-dev --optimize-autoloader`.
- Envie os arquivos compilados de `public/build` ou execute `npm run build` antes do deploy.
- Envie também `storage/app/public/products` para preservar as fotos do catálogo.

## Banco de demonstração

Somente em um banco que pode ser apagado, rode por SSH na pasta que contém `artisan`:

```bash
php artisan migrate:fresh --seed --force
php artisan storage:link
php artisan optimize:clear
```

O comando cria a estrutura e insere categorias, produtos, imagens cadastradas, contas e pedidos de demonstração. As imagens continuam dependendo dos arquivos enviados para `storage/app/public/products`.

## Atualização de produção com dados reais

Não use `migrate:fresh`. Execute apenas migrations novas:

```bash
php artisan migrate --force
php artisan optimize:clear
```

## Fila e webhooks

Como o webhook do Asaas é colocado na fila `database`, mantenha um worker ativo:

```bash
php artisan queue:work --tries=3
```

Configure esse processo pelo recurso de processos/cron da hospedagem de acordo com o plano contratado. Sem worker, webhooks entram na tabela `jobs`, mas não são processados.

## Alertas por e-mail (opcional)

O projeto já registra falhas críticas de checkout, pagamentos, frete e webhook em `storage/logs/laravel.log`. O envio de alertas por e-mail não é ativado automaticamente: essa decisão depende do e-mail remetente e do destinatário aprovados para produção.

Para usar uma conta Gmail como remetente, crie uma **senha de app** na conta Google com a verificação em duas etapas ativada. No `.env` do servidor, configure os valores abaixo com dados reais. Nunca versione ou compartilhe a senha de app.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alertas@sua-loja.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alertas@sua-loja.com
MAIL_FROM_NAME="Malu Store"
ALERT_EMAIL=responsavel@sua-loja.com
```

Após salvar o `.env`, limpe e recrie o cache de configuração:

```bash
php artisan optimize:clear
php artisan config:cache
```

`MAIL_USERNAME` é a conta que envia os e-mails; `ALERT_EMAIL` é quem os recebe e pode ser o mesmo endereço. Caso a conta remetente seja uma caixa criada na Hostinger, mantenha o host SMTP fornecido pela própria Hostinger em vez de `smtp.gmail.com`.

> A ligação de `ALERT_EMAIL` ao disparo de notificações ainda deve ser implementada antes de depender de e-mails em produção. Até lá, os registros críticos permanecem disponíveis no log do Laravel.
