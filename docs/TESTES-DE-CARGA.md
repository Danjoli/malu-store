# Testes de carga

O cenário em `tests/Load/storefront.js` mede as páginas públicas sem criar pedidos, usuários ou pagamentos. Ele valida taxa de erro, latência p95/p99, conteúdo e propagação do identificador de requisição.

## Execução segura

Execute primeiro em homologação, com uma cópia sanitizada do banco e monitoramento de CPU, memória, conexões MySQL, filas e erros:

```bash
k6 run -e BASE_URL=https://staging.exemplo.com -e VUS=20 tests/Load/storefront.js
```

Os valores podem ser ajustados com `VUS`, `RAMP_UP` e `DURATION`. O padrão sobe gradualmente até 20 usuários virtuais, mantém a carga por dois minutos e reduz em 15 segundos.

O script bloqueia a URL oficial da Malu Store por padrão. Uma execução intencional em produção exige `ALLOW_PRODUCTION=true`, janela aprovada, alerta aos responsáveis e carga inicial baixa.

## Critérios iniciais

- menos de 1% de requisições com falha;
- p95 menor que 800 ms;
- p99 menor que 1,5 s;
- mais de 99% das verificações aprovadas;
- `/health` saudável antes e depois do ensaio;
- ausência de crescimento persistente da fila e de erros 5xx.

Registre data, commit, ambiente, volume, resultado e métricas de infraestrutura. Compare cada execução com a anterior antes de aumentar a carga. Não use este cenário para concluir capacidade de checkout: pagamentos exigem um cenário separado, exclusivamente no sandbox do Asaas.
