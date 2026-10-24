# Ambiente de homologação

O ambiente `staging` é uma instalação integral e isolada da Malu Store. Ele não
compartilha banco, Redis, bucket, credenciais de integrações nem sessões com a
produção.

## Endereço e proteção

- Aplicação: `https://staging.malu-store.com`
- O acesso às páginas web exige autenticação HTTP Basic.
- Todas as respostas web recebem `X-Robots-Tag: noindex, nofollow, noarchive`.
- `/up` continua disponível para o health check do deploy.
- As APIs mantêm suas próprias autenticações, permitindo webhooks de sandbox.

## Recursos obrigatoriamente separados

1. Banco MySQL cujo nome contenha `staging`.
2. Banco ou projeto Redis exclusivo, com `REDIS_PREFIX` contendo `staging`.
3. Bucket R2 exclusivo cujo nome contenha `staging`.
4. Asaas configurado como `sandbox`.
5. Melhor Envio configurado como `sandbox`.
6. E-mail usando `log` ou `array`, sem entrega externa.
7. Ambiente Sentry identificado como `staging`.

O script de deploy recusa a implantação se qualquer uma dessas proteções não
estiver configurada.

## Preparação inicial na hospedagem

1. Criar o subdomínio `staging.malu-store.com` com diretório próprio.
2. Criar o banco e usuário MySQL exclusivos.
3. Criar o diretório `public_html` da homologação.
4. Copiar `.env.staging.example` para `public_html/.env` e preencher apenas no
   servidor.
5. Gerar uma `APP_KEY` exclusiva; nunca copiar a chave da produção.

## Ambiente `staging` no GitHub

Criar o environment `staging` em **Settings > Environments**, sem copiar
segredos de serviços da produção. Adicionar:

- `STAGING_SSH_PRIVATE_KEY`
- `STAGING_SSH_KNOWN_HOSTS`
- `STAGING_SSH_HOST`
- `STAGING_SSH_PORT`
- `STAGING_SSH_USER`
- `STAGING_APP_ROOT` (exemplo:
  `/home/usuario/domains/staging.malu-store.com`)

O workflow **Deploy de homologação** aceita um commit, branch ou tag. Ao
contrário da produção, ele pode implantar uma branch para revisão antes do
merge.

## Dados fictícios

Depois do primeiro deploy, popular somente o banco isolado:

```bash
php artisan db:seed --force
```

Nunca restaure diretamente um backup de produção. Se no futuro forem usados
dados derivados de produção, eles deverão ser anonimizados antes da importação.

## Processos agendados

Criar três cron jobs com o caminho da homologação:

```text
artisan schedule:run
artisan queue:work redis --queue=critical --stop-when-empty --tries=3 --timeout=120
artisan queue:work redis --queue=default --stop-when-empty --tries=3 --timeout=120
```

Cada comando deve usar um arquivo `flock` próprio, diferente dos arquivos da
produção.

## Diagnóstico dos serviços

Depois de qualquer alteração no `.env`, execute:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan queue:restart
```

Valide a prontidão completa:

```bash
curl --user 'USUARIO' https://staging.malu-store.com/health
```

A resposta deve ter `status: ok` e indicar `database`, `queue` e `storage` como
verdadeiros. O check da fila envia `PING` ao Redis; o check de storage grava e
remove um objeto temporário no R2. Não coloque a senha diretamente no comando:
sem `:senha`, o `curl` solicita o valor sem gravá-lo no histórico.

Para um diagnóstico detalhado e seguro de Redis e R2, siga
[INFRAESTRUTURA.md](INFRAESTRUTURA.md). Nunca mostre `REDIS_URL`, chaves AWS/R2 ou
o conteúdo completo do `.env`.

## Teste de carga

O cenário existente aceita a autenticação da homologação:

```bash
k6 run -e BASE_URL=https://staging.malu-store.com \
  -e BASIC_AUTH_USER=usuario \
  -e BASIC_AUTH_PASSWORD=senha \
  tests/Load/storefront.js
```

As credenciais não devem ser gravadas no Git ou no histórico compartilhado do
terminal.

Use `PROFILE=smoke`, `baseline`, `growth` ou `stress` conforme
[TESTES-DE-CARGA.md](TESTES-DE-CARGA.md); não comece diretamente pelo perfil de
maior carga.
