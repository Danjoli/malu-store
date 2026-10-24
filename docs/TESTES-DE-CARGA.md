# Testes de carga

O cenário em `tests/Load/storefront.js` mede as páginas públicas sem criar pedidos, usuários ou pagamentos. Ele valida taxa de erro, latência p95/p99, conteúdo e propagação do identificador de requisição.

## Execução segura

Execute primeiro em homologação, com dados sintéticos e monitoramento de CPU,
memória, conexões MySQL, Redis, filas, R2 e erros no Sentry.

## Perfis

| Perfil | Usuários virtuais | Uso recomendado |
|---|---:|---|
| `smoke` | 1 | Após cada deploy |
| `baseline` | 20 | Validação periódica |
| `growth` | 50 | Antes de campanhas ou crescimento esperado |
| `stress` | 100 | Somente em janela controlada de homologação |

Defina as credenciais HTTP Basic sem gravar a senha no histórico:

```bash
export BASE_URL=https://staging.malu-store.com
export BASIC_AUTH_USER='usuario-da-homologacao'
read -s -p 'Senha da homologação: ' BASIC_AUTH_PASSWORD
export BASIC_AUTH_PASSWORD

k6 run -e PROFILE=smoke tests/Load/storefront.js
k6 run -e PROFILE=baseline tests/Load/storefront.js
```

Para um ensaio específico, `VUS`, `RAMP_UP` e `DURATION` substituem o perfil.
Comece por `smoke`, avance um nível por vez e interrompa se houver erro, saturação
ou crescimento persistente da fila.

O script bloqueia a URL oficial da Malu Store por padrão. Uma execução intencional em produção exige `ALLOW_PRODUCTION=true`, janela aprovada, alerta aos responsáveis e carga inicial baixa.

## Critérios iniciais

- menos de 1% de requisições com falha;
- p95 menor que 800 ms;
- p99 menor que 1,5 s;
- mais de 99% das verificações aprovadas;
- `/health` saudável antes, durante e depois do ensaio;
- ausência de crescimento persistente da fila e de erros 5xx.

Registre data, commit, ambiente, perfil, volume, resultado e métricas de
infraestrutura. Compare cada execução com a anterior antes de aumentar a carga.
Não use este cenário para concluir capacidade de checkout: pagamentos exigem um
cenário separado, exclusivamente nos sandboxes do Asaas e Melhor Envio.

O arquivo [../tests/Load/README.md](../tests/Load/README.md) contém o lembrete
rápido ao lado do script; este documento é a referência operacional principal.
