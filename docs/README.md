# Documentação da Malu Store

Este diretório reúne a referência técnica do projeto. Para conhecer o sistema, comece pela arquitetura; para publicar uma versão, siga a implantação do início ao fim.

| Documento | Quando consultar | Conteúdo |
|---|---|---|
| [ARQUITETURA.md](ARQUITETURA.md) | Para entender ou evoluir o código. | Estrutura, fluxos, front-end, pagamentos, webhooks, interface e testes. |
| [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md) | Para alterar tabelas ou dados de demonstração. | Relações, migrations, factories, seeders, imagens e evolução do banco. |
| [IMPLANTACAO.md](IMPLANTACAO.md) | Para publicar ou atualizar o servidor. | Checklist da Hostinger, variáveis de ambiente, banco, arquivos públicos, fila e alertas. |
| [HOMOLOGACAO.md](HOMOLOGACAO.md) | Para testar uma versão sem afetar clientes reais. | Ambiente isolado, proteções, deploy, dados fictícios e teste de carga. |
| [INFRAESTRUTURA.md](INFRAESTRUTURA.md) | Para configurar ou diagnosticar serviços externos. | Redis, filas, Cloudflare R2, health checks e separação por ambiente. |
| [ESCALABILIDADE.md](ESCALABILIDADE.md) | Para planejar crescimento e capacidade. | Estado atual, limites, métricas e critérios para aumentar carga. |
| [AUTENTICACAO.md](AUTENTICACAO.md) | Para mexer em login, senha ou permissões. | Guards, sessões, recuperação de senha, requisitos e limites de acesso. |
| [TESTES_INTEGRACOES.md](TESTES_INTEGRACOES.md) | Para evoluir Asaas ou Melhor Envio. | Fixtures, falhas simuladas, contratos e idempotência. |
| [TESTES-DE-CARGA.md](TESTES-DE-CARGA.md) | Para medir a vitrine com segurança. | Perfis k6, limites, execução em homologação e registro dos resultados. |

## Atalhos por tarefa

- Novo produto ou categoria: consulte **Banco de dados** e depois **Arquitetura**.
- Alteração de checkout, pagamento ou frete: consulte **Arquitetura** antes de mudar o fluxo.
- Deploy: siga **Implantação**; não use comandos de banco destrutivos em dados reais.
- Redis, R2 ou fila: consulte **Infraestrutura** antes de alterar o `.env`.
- Teste de desempenho: siga **Testes de carga** e comece sempre por homologação.
- Problema de acesso: consulte **Autenticação** e confira qual guard está envolvido (`web` ou `admin`).
