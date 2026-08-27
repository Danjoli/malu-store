# Documentação da Malu Store

Este diretório reúne a referência técnica do projeto. Para conhecer o sistema, comece pela arquitetura; para publicar uma versão, siga a implantação do início ao fim.

| Documento | Quando consultar | Conteúdo |
|---|---|---|
| [ARQUITETURA.md](ARQUITETURA.md) | Para entender ou evoluir o código. | Estrutura, fluxos, front-end, pagamentos, webhooks, interface e testes. |
| [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md) | Para alterar tabelas ou dados de demonstração. | Relações, migrations, factories, seeders, imagens e evolução do banco. |
| [IMPLANTACAO.md](IMPLANTACAO.md) | Para publicar ou atualizar o servidor. | Checklist da Hostinger, variáveis de ambiente, banco, arquivos públicos, fila e alertas. |
| [AUTENTICACAO.md](AUTENTICACAO.md) | Para mexer em login, senha ou permissões. | Guards, sessões, recuperação de senha, requisitos e limites de acesso. |

## Atalhos por tarefa

- Novo produto ou categoria: consulte **Banco de dados** e depois **Arquitetura**.
- Alteração de checkout, pagamento ou frete: consulte **Arquitetura** antes de mudar o fluxo.
- Deploy: siga **Implantação**; não use comandos de banco destrutivos em dados reais.
- Problema de acesso: consulte **Autenticação** e confira qual guard está envolvido (`web` ou `admin`).
