# Implantação na Hostinger

Checklist para publicar uma cópia de demonstração ou uma versão de produção da Malu Store.

> A aplicação requer PHP 8.4 ou 8.5 e Laravel 13. Confirme a versão do PHP usada pelo servidor web e pelo worker de filas antes do deploy.

## Antes de começar

- Configure o `.env` com o banco MySQL correto, URL do site, `APP_ENV=production` e `APP_DEBUG=false`.
- Instale dependências PHP com `composer install --no-dev --optimize-autoloader`.
- Execute `npm ci && npm run build` no artefato de cada release antes do deploy.
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

## Assets públicos e SEO

- `public/build` é sempre gerado a partir de `resources`, `package-lock.json` e `vite.config.js`; ele não é versionado.
- A CI compila os assets em todos os commits. O deploy deve executar `npm ci && npm run build` em uma pasta de release limpa, antes da troca atômica do diretório público.
- O Vite limpa `public/build` antes de cada compilação, evitando arquivos com hash obsoleto. Não copie esse diretório entre releases.
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

## Monitoramento externo

O workflow **Monitor de produção** consulta `/up` e `/produtos` a cada 15 minutos, com três novas tentativas para falhas transitórias. Se algum endpoint continuar indisponível, ele abre uma issue de prioridade alta no GitHub. Enquanto a primeira issue estiver aberta, novas execuções falhas não criam duplicatas.

Também é possível executar o monitor manualmente pela aba Actions. Depois de resolver um incidente, valide os dois endpoints, registre a causa e a correção na issue e só então feche-a. A próxima indisponibilidade poderá criar uma nova ocorrência.

## Backup e recuperação

O backup diário reúne um dump consistente do MySQL e `storage/app/public`, criptografa o pacote com AES-256 e grava um checksum SHA-256. A retenção padrão é de 14 cópias. Credenciais, nome do banco e chave de criptografia ficam fora de `public_html`, com permissão somente para o usuário da hospedagem (`~/.malu-store-backup.cnf`, `~/.malu-store-backup-database` e `~/.malu-store-backup.key`).

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
