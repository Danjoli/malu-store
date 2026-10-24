# Plano de escalabilidade

## Estado atual

A aplicação roda em uma instância Hostinger, com MySQL como banco principal.
Homologação e produção usam Redis gerenciado para cache, sessões e filas, e
Cloudflare R2 para imagens de produtos. Os recursos e prefixos são separados por
ambiente. A configuração e os diagnósticos estão em
[INFRAESTRUTURA.md](INFRAESTRUTURA.md).

A hospedagem desabilita `proc_open` e não oferece supervisão contínua adequada
para Laravel Horizon. Por isso, os workers Redis são curtos, usam
`--stop-when-empty` e são iniciados pelo agendador com `flock`.

O catálogo já foi validado com um milhão de usuários sintéticos e um milhão de
produtos em homologação. Esse volume demonstra o comportamento das consultas,
mas não substitui teste concorrente, métricas do servidor ou projeção de tráfego
real.

## Capacidade e sinais de crescimento

Aumente recursos ou reveja a arquitetura quando houver tendência persistente,
não por um pico isolado. Acompanhe:

- latência p50, p95 e p99 das páginas públicas;
- taxa de respostas 5xx e timeouts;
- CPU, memória, I/O e conexões MySQL;
- consultas lentas e planos de execução;
- uso, latência, erros e hit rate do Redis;
- quantidade de jobs, idade do mais antigo, tentativas e falhas por fila;
- latência e erros do R2, Asaas e Melhor Envio;
- volume, custo e taxa de erros registrados no Sentry.

## Evolução recomendada

### 1. Otimizar antes de ampliar

1. Identificar consultas lentas com dados representativos.
2. Confirmar índices com `EXPLAIN`, evitando adicionar índices sem evidência.
3. Cachear apenas resultados de leitura que possam ser descartados.
4. Manter paginação e limites em listagens administrativas e públicas.
5. Medir novamente com os perfis definidos em
   [TESTES-DE-CARGA.md](TESTES-DE-CARGA.md).

### 2. Redis

- manter TLS, autenticação e prefixos exclusivos;
- acompanhar memória, expiração, evicções e reconexões;
- aumentar o plano antes de atingir o limite de memória;
- nunca usar `FLUSHALL` ou `FLUSHDB` em produção;
- testar indisponibilidade e rollback em homologação.

Se o serviço permitir, sessões/filas e cache podem continuar em bancos lógicos
separados. Em uma futura arquitetura com várias aplicações ou políticas de
retenção distintas, use instâncias/projetos separados.

### 3. Filas

As filas `critical` e `default` devem ser acompanhadas separadamente. Se jobs
críticos passarem a aguardar, aumente a frequência dos workers ou migre a execução
para uma VPS/container com supervisor contínuo. Antes da migração:

1. iniciar os novos workers;
2. manter o consumidor antigo enquanto houver jobs pendentes;
3. confirmar idempotência de pagamentos, webhooks e etiquetas;
4. ensaiar rollback sem perder nem duplicar jobs.

### 4. Banco de dados

Quando a instância atual deixar de atender:

1. otimizar consultas e índices;
2. aumentar recursos do MySQL;
3. separar tarefas analíticas e relatórios pesados;
4. considerar réplica de leitura somente quando a aplicação estiver preparada
   para consistência eventual;
5. manter backups e restaurações testados em qualquer mudança.

### 5. Aplicação horizontal

Redis e R2 removem estado local de sessões, cache, filas e imagens, preparando a
aplicação para mais de uma instância web. Antes de escalar horizontalmente,
confirme:

- releases idênticas e configuração consistente;
- balanceador com health check;
- nenhuma dependência de arquivos gravados apenas no servidor local;
- migrations compatíveis com implantação sem indisponibilidade;
- workers, logs, métricas e alertas centralizados.

## Critérios para aumentar a carga de teste

1. `smoke` aprovado após o deploy.
2. `baseline` estável sem aumento de erros ou filas.
3. `growth` somente quando o tráfego real se aproximar do baseline ou antes de
   campanhas.
4. `stress` somente em homologação, em janela controlada e sem backups,
   importações ou manutenção simultânea.
5. Produção aceita apenas `smoke` deliberado, com `ALLOW_PRODUCTION=true` e fora
   do horário de pico.

## Critérios de segurança para qualquer mudança

- suíte completa e testes de navegador aprovados;
- teste de carga aprovado em homologação;
- falha e reconexão de Redis verificadas;
- escrita, leitura e exclusão no R2 verificadas;
- restauração de backup validada;
- workers e alertas verificados;
- rollback documentado e ensaiado.
