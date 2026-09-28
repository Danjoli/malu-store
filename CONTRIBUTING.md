# Contribuindo com a Malu Store

Este repositorio usa um fluxo simples baseado em Issues, branches curtas e Pull Requests.

## Fluxo de trabalho

1. Crie ou selecione uma Issue com objetivo e criterios de aceitacao.
2. Crie uma branch a partir de `main`.
3. Faca commits pequenos e relacionados ao mesmo objetivo.
4. Execute as verificacoes locais.
5. Abra um Pull Request ligado a Issue.
6. Aguarde a verificacao automatica antes do merge.

## Nome das branches

Use nomes curtos e descritivos:

- `feat/numero-da-issue-resumo`
- `fix/numero-da-issue-resumo`
- `docs/numero-da-issue-resumo`
- `refactor/numero-da-issue-resumo`
- `test/numero-da-issue-resumo`
- `chore/numero-da-issue-resumo`

Exemplo: `fix/42-validacao-do-checkout`.

## Mensagens de commit

Use o formato `tipo: descricao curta`, com verbo no presente e um unico assunto por commit.

- `feat:` nova funcionalidade
- `fix:` correcao de defeito
- `docs:` documentacao
- `style:` formatacao sem mudanca de comportamento
- `refactor:` reorganizacao sem nova funcionalidade
- `test:` testes
- `build:` dependencias ou compilacao
- `ci:` automacao do GitHub
- `chore:` manutencao geral
- `security:` protecoes e correcoes de seguranca

Exemplos:

```text
feat: adiciona filtro por categoria
fix: impede compra acima do estoque
test: cobre falha de assinatura do webhook
```

Evite mensagens vagas como `ajustes`, `correcao`, `teste` ou `pente fino`.

## Verificacoes locais

Antes de abrir o Pull Request, execute:

```bash
vendor/bin/pint --test
php artisan test
npm run build
composer audit
```

## Seguranca

Nunca versione `.env`, credenciais, tokens, chaves privadas ou dados reais de clientes. Use valores ficticios em exemplos e remova informacoes sensiveis de logs e capturas de tela.
