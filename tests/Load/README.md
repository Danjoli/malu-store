# Testes de carga da vitrine

Os perfis exercitam somente endpoints de leitura (`/`, `/produtos` e `/health`).
Execute preferencialmente contra homologação e acompanhe CPU, memória, banco, Redis,
filas e erros no Sentry durante todo o teste.

## Perfis

| Perfil | Usuários virtuais | Uso recomendado |
|---|---:|---|
| `smoke` | 1 | Após cada deploy |
| `baseline` | 20 | Validação periódica |
| `growth` | 50 | Antes de campanhas ou aumento de tráfego |
| `stress` | 100 | Somente em janela controlada de homologação |

## Execução em homologação

Defina as credenciais da proteção básica como variáveis de ambiente para que não
fiquem gravadas no comando ou no histórico do terminal:

```bash
export BASE_URL=https://staging.malu-store.com
export BASIC_AUTH_USER='usuario-da-homologacao'
read -s -p 'Senha da homologação: ' BASIC_AUTH_PASSWORD
export BASIC_AUTH_PASSWORD

k6 run -e PROFILE=smoke tests/Load/storefront.js
k6 run -e PROFILE=baseline tests/Load/storefront.js
```

Use `growth` quando o tráfego real se aproximar de 20 usuários simultâneos. Use
`stress` apenas após o perfil `growth` passar e nunca junto de importações, backups
ou manutenção do banco.

## Critérios automáticos

- menos de 1% de falhas HTTP;
- mais de 99% das verificações aprovadas;
- p95 abaixo de 800 ms;
- p99 abaixo de 1.500 ms.

Produção permanece bloqueada por padrão. Uma execução deliberada exige
`ALLOW_PRODUCTION=true` e deve usar apenas o perfil `smoke`, fora de horário de pico.
