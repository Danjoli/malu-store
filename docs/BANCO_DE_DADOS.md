# Banco de dados da Malu Store

Este documento descreve o modelo de dados, os relacionamentos e os comandos para criar ou popular o banco da aplicação.

## Visão geral

O banco é criado pelas migrations do Laravel em `database/migrations`. Não edite tabelas manualmente em produção: crie uma migration para toda alteração estrutural.

## Entidades e relacionamentos

```text
Categoria 1 ── N Produtos 1 ── N Imagens
                         └── N Variantes

Cliente 1 ── N Endereços
Cliente 1 ── N Carrinhos 1 ── N Itens de carrinho ── 1 Variante
Cliente 1 ── N Pedidos  1 ── N Itens do pedido
                          └── 1 Envio
Cliente N ── N Produtos (favoritos)
```

## Tabelas principais

| Tabela | Responsabilidade | Relações principais |
|---|---|---|
| `users` | Clientes da loja. | Endereços, carrinhos, pedidos e favoritos. |
| `admins` | Acessos ao painel administrativo. | Não se mistura com clientes. |
| `categories` | Agrupa produtos do catálogo. | Possui muitos produtos. |
| `products` | Produto base, slug, preço, descrição e ativo. | Categoria, imagens, variantes e favoritos. |
| `product_images` | Caminhos das fotos do produto. | Pertence a um produto. |
| `product_variants` | Cor, tamanho e estoque. | Pertence a um produto; é selecionada no carrinho. |
| `carts` | Carrinho por cliente e status. | Possui itens de carrinho. |
| `cart_items` | Variante, quantidade e snapshots do carrinho. | Pertence a carrinho e variante. |
| `addresses` | Endereços salvos do cliente. | Pertence a um cliente. |
| `orders` | Pedido, totais, pagamento e snapshot de entrega. | Cliente, itens e envio. |
| `order_items` | Itens comprados com snapshots de produto. | Pertence a pedido e variante quando aplicável. |
| `shipments` | Frete, transportadora, rastreio e status. | Pertence a um pedido. |
| `favorites` | Produtos salvos por cada cliente. | Liga `users` a `products`. |
| `password_reset_tokens` | Tokens de recuperação de senha de clientes. | Associado ao e-mail do cliente. |
| `admin_password_reset_tokens` | Tokens de recuperação de senha administrativa. | Associado ao e-mail administrativo. |
| `jobs` / `failed_jobs` | Fila de tarefas do Laravel. | Webhooks do Asaas usam esta estrutura. |

## Snapshots e histórico

Pedidos armazenam os dados necessários para preservar o histórico: nome, preço, foto, cor, tamanho e endereço. Assim, mudar um produto ou endereço depois da compra não altera o pedido já realizado.

O estoque está em `product_variants.stock`. Ele é baixado apenas quando o webhook confirma o pagamento pela primeira vez.

## Dados de demonstração

O `DatabaseSeeder` chama dois seeders:

| Seeder | Conteúdo |
|---|---|
| `StoreCatalogSeeder` | Categorias, produtos, imagens e variantes. |
| `StoreDemoDataSeeder` | Cliente, administrador, endereços, pedidos, envios e carrinho de exemplo. |

Para testes de Feature, use `User::factory()->withStoreData()->create()` quando o cenário precisar de um cliente completo. Esse estado cria dois endereços, um favorito, um pedido com item e um envio, sem alterar o comportamento padrão de `User::factory()`.

Em ambiente local ou de demonstração, para recriar tudo do zero:

```bash
php artisan migrate:fresh --seed
```

No servidor, somente em um banco que pode ser apagado:

```bash
php artisan migrate:fresh --seed --force
```

> Atenção: `migrate:fresh` apaga todas as tabelas do banco configurado no `.env`.

Os produtos do catálogo de demonstração recebem slug diretamente no seeder, pois o `DatabaseSeeder` desativa eventos de modelo durante a carga. Produtos criados pela aplicação recebem slug pelo `ProductObserver`.

## Imagens do catálogo

O banco guarda apenas o caminho da imagem na tabela `product_images`; um produto pode ter várias imagens. O catálogo de demonstração cria três registros por produto, e `ProductImageFactory` escolhe uma imagem de demonstração quando for usada em testes. Para cenários que precisem de uma galeria completa, use `Product::factory()->withGallery()->create()`.

Os arquivos devem existir em:

```text
storage/app/public/products
```

Para expô-los pela web, execute:

```bash
php artisan storage:link
```

Em uma implantação, envie a pasta `storage/app/public/products` junto com o projeto. Executar os seeders sem enviar os arquivos não faz as imagens aparecerem. No painel, a tela de detalhes do produto permite substituir a galeria com até oito imagens válidas; ao substituir, os registros e arquivos anteriores são removidos.

## Boas práticas para evoluir

1. Crie uma migration nova para cada mudança de tabela ou índice.
2. Crie ou ajuste Factory e Seeder quando a mudança precisar de dados de teste.
3. Adicione teste de Feature para regras críticas que dependem da nova estrutura.
4. Nunca use `migrate:fresh` em produção com dados reais.
5. Mantenha segredos e credenciais somente no `.env`, nunca em seeders ou migrations.
