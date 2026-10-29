# Infraestrutura e serviços externos

Este documento descreve a configuração operacional atual da Malu Store. Valores
secretos pertencem somente ao `.env` de cada ambiente e nunca devem ser copiados
para o Git, documentação, logs ou comandos compartilhados.

## Matriz por ambiente

| Recurso | Local | Homologação | Produção |
|---|---|---|---|
| Banco principal | MySQL `malu_store` | MySQL exclusivo com `staging` no nome | MySQL exclusivo de produção |
| Cache | `database` por padrão | Redis com fallback MySQL | Redis com fallback MySQL |
| Sessões | `file` por padrão | MySQL | MySQL |
| Filas | `database` por padrão | Redis | Redis |
| Imagens | disco Laravel `public` | Cloudflare R2, bucket exclusivo | Cloudflare R2, bucket exclusivo |
| Pagamento e frete | sandbox | sandbox | produção |
| E-mail | SMTP local ou `log` | `log` ou `array` | SMTP |
| Sentry | opcional | environment `staging` | environment `production` |

O `.env.example` é voltado ao desenvolvimento local. O
`.env.staging.example` é a referência segura para homologação. Produção usa os
mesmos drivers externos da homologação, mas com credenciais, prefixo Redis,
bucket, domínio de assets e integrações totalmente separados.

## Redis

Redis armazena cache, sessões e filas nos ambientes hospedados:

```env
CACHE_STORE=failover
SESSION_DRIVER=database
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_URL=
REDIS_PREFIX=malu-store-ambiente-
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_CONNECT_TIMEOUT=3
REDIS_READ_TIMEOUT=10
REDIS_MAX_RETRIES=2
REDIS_BACKOFF_BASE=100
REDIS_BACKOFF_CAP=1000
```

Use a URL TLS fornecida pelo serviço gerenciado. Quando `REDIS_URL` estiver
preenchida, ela concentra host, porta, usuário, senha e protocolo. Nunca reutilize
o mesmo `REDIS_PREFIX` entre local, homologação e produção. O banco lógico `0` é
usado pela conexão padrão (sessões e filas) e o banco `1` pela conexão de cache,
quando o provedor oferece bancos lógicos.

Após alterar qualquer variável:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan queue:restart
```

Diagnóstico sem exibir credenciais:

```bash
php artisan tinker --execute="dump([
    'cache' => Cache::store()->put('diagnostic:cache', 'ok', 30),
    'cache_value' => Cache::store()->get('diagnostic:cache'),
    'queue' => config('queue.default'),
    'session' => config('session.driver'),
]); Cache::store()->forget('diagnostic:cache');"
```

O cache `failover` tenta Redis primeiro e usa a tabela `cache` do MySQL durante
uma falha transitória. Sessões ficam no MySQL para que uma oscilação do Redis não
desconecte clientes nem derrube páginas. A fila permanece no Redis: quando ela
está indisponível, webhooks devem responder com erro para que o fornecedor tente
novamente. Predis e PhpRedis usam timeout curto, até duas reconexões e espera
exponencial com jitter.

O endpoint `/health` guarda seu resultado por 30 segundos em cache de arquivo,
independente do Redis, e envia `PING` à conexão usada pela fila. Ele responde
`503` se banco, fila ou armazenamento não estiverem disponíveis.

Não execute `FLUSHALL`, `FLUSHDB` ou limpeza global em produção. Uma limpeza
indiscriminada pode encerrar sessões, remover jobs e apagar caches de outras
aplicações. Para rollback temporário, altere um driver de cada vez para
`database`, recrie o cache de configuração e mantenha o worker antigo ativo até a
fila Redis ficar vazia.

## Filas

Os ambientes hospedados usam duas filas:

- `critical`: pagamentos e webhooks;
- `default`: tarefas comuns, incluindo operações de envio.

Como a Hostinger não mantém um supervisor Laravel Horizon, workers curtos devem
ser disparados pelo agendador com `flock`, cada um com arquivo de trava próprio:

```text
artisan queue:work redis --queue=critical --stop-when-empty --tries=3 --timeout=120
artisan queue:work redis --queue=default --stop-when-empty --tries=3 --timeout=120
```

Use também `artisan schedule:run` a cada minuto. Depois de um deploy, o script de
ativação executa `queue:restart`. Consulte falhas antes de repeti-las:

```bash
php artisan queue:failed
php artisan queue:retry ID
```

## Cloudflare R2

As imagens de produtos usam o disco padrão configurado por
`FILESYSTEM_DISK`. Em homologação e produção ele deve ser `s3`, apontando para o
bucket R2 correspondente:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=malu-store-ambiente
AWS_ENDPOINT=https://ACCOUNT_ID.r2.cloudflarestorage.com
AWS_URL=https://assets-do-ambiente.exemplo.com
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Para R2, a região correta é `auto`, não `us-auto`. `AWS_ENDPOINT` é o endpoint S3
da conta; `AWS_URL` é o domínio público de leitura. Cada ambiente deve ter token,
bucket e domínio próprios. O token precisa somente das permissões necessárias de
leitura, gravação e exclusão naquele bucket.

O CORS do domínio de assets deve permitir somente as origens web necessárias. O
domínio público deve usar HTTPS e TLS moderno. Para validar escrita, leitura e
exclusão sem deixar arquivos:

```bash
php artisan tinker --execute="
\$disk = Storage::disk('s3');
\$path = 'healthchecks/manual-'.Str::uuid().'.txt';
\$written = \$disk->put(\$path, 'ok');
\$exists = \$written && \$disk->exists(\$path);
\$deleted = \$exists && \$disk->delete(\$path);
dump(compact('written', 'exists', 'deleted'));
"
```

O resultado esperado é `true` nos três campos. `/health` realiza uma verificação
equivalente no disco padrão e remove o objeto temporário.

Para copiar imagens locais ao R2, faça primeiro uma simulação e depois execute a
cópia verificada. A origem não é apagada:

```bash
php artisan storage:migrate-products --from=public --to=s3
php artisan storage:migrate-products --from=public --to=s3 --execute
```

## Verificações operacionais

- `/up`: confirma que o Laravel iniciou; é usado pelo deploy e pelo monitor.
- `/health`: testa MySQL, fila/Redis e o disco padrão; não o use como substituto
  de métricas e alertas.
- Sentry: separa eventos por `SENTRY_ENVIRONMENT` e não deve coletar PII.
- Monitor de produção: consulta `/up` e `/produtos` a cada 15 minutos.
- Backups: protegem MySQL e arquivos locais legados. Objetos do R2 exigem um
  mecanismo próprio de retenção e recuperação compatível com o plano contratado.

Nunca exponha a resposta completa de configuração, URLs com credenciais ou o
conteúdo do `.env` em tickets, capturas de tela ou logs.
