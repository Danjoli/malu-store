# Plano de escalabilidade

## Estado atual

A produção usa uma única instância Hostinger. Cache, sessões e filas estão no MySQL; uploads ficam em `storage/app/public`. O PHP possui a extensão Redis, mas não existe servidor Redis acessível. Também não há bucket S3 configurado. A hospedagem desabilita `proc_open`, portanto não é adequada para Laravel Horizon ou supervisão contínua de múltiplos workers.

## Pré-requisitos externos

- Redis gerenciado com TLS, autenticação, backups e região próxima à aplicação.
- Object storage compatível com S3, bucket privado para origem e CDN HTTPS para leitura pública.
- Ambiente de homologação com as mesmas integrações.
- Plataforma que mantenha processos de fila continuamente supervisionados, ou um worker separado em VPS/container.

Credenciais devem ficar apenas no Environment protegido e no `.env` de cada ambiente. Nunca devem entrar no repositório.

## Migração sem indisponibilidade

### 1. Object storage e CDN

1. Criar bucket, política de menor privilégio, CORS e CDN.
2. Alterar a aplicação para gerar URLs pelo disco configurado, eliminando caminhos `/storage` fixos.
3. Copiar os uploads existentes para `products/` no bucket sem removê-los do servidor.
4. Comparar quantidade, tamanho e checksum dos objetos.
5. Ativar leitura pelo CDN em homologação e executar a suíte de navegador.
6. Em produção, ativar `FILESYSTEM_DISK=s3`; manter os arquivos locais durante pelo menos sete dias para rollback.
7. Aplicar cache público longo para nomes imutáveis e curto para objetos substituíveis.

### 2. Redis para cache e rate limiting

1. Configurar conexões separadas e prefixos exclusivos por ambiente.
2. Validar TLS, latência, expiração e comportamento quando o serviço estiver indisponível.
3. Trocar primeiro apenas `CACHE_STORE=redis` e acompanhar erros, memória e hit rate.
4. O rate limiting passa a compartilhar o mesmo cache entre instâncias.
5. Rollback: retornar `CACHE_STORE=database`; caches podem ser descartados com segurança.

### 3. Sessões

1. Confirmar persistência, TTL e criptografia das sessões em homologação.
2. Trocar `SESSION_DRIVER=redis` em janela de baixo tráfego.
3. A troca pode solicitar novo login aos usuários; não altera pedidos nem carrinhos persistidos no banco.
4. Rollback: retornar ao driver `database`.

### 4. Filas por criticidade

Separar `payments`, `webhooks`, `shipping` e `default`. Pagamentos e webhooks recebem workers próprios e prioridade maior. Antes da troca:

1. iniciar workers Redis supervisionados;
2. manter o worker do banco consumindo a fila antiga;
3. alterar `QUEUE_CONNECTION=redis` e publicar novos jobs nas filas nomeadas;
4. aguardar a fila do banco chegar a zero;
5. manter o worker antigo disponível por uma janela de rollback.

Métricas mínimas: jobs publicados/concluídos por minuto, idade do job mais antigo, tamanho por fila, falhas, tentativas e tempo de execução. Alertar quando a idade ou as falhas ultrapassarem os limites operacionais.

## Critérios para ativação

- suíte completa e testes de navegador aprovados em homologação;
- teste de falha e reconexão do Redis;
- restauração de backup validada;
- checksums dos uploads conferidos;
- workers reiniciam automaticamente;
- painéis e alertas testados;
- procedimento de rollback ensaiado.

Sem Redis, bucket/CDN e supervisão fornecidos, a aplicação deve permanecer nos drivers atuais. Essa decisão mantém a loja funcional e evita uma migração parcial que perderia sessões, jobs ou imagens.
