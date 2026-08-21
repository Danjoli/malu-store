# Arquitetura da Malu Store

Este documento descreve como a aplicação funciona hoje: estrutura, dados, compras, pagamento, envios, interface e testes.

## Visão geral

A Malu Store é um e-commerce em Laravel 12 para catálogo de moda, carrinho, checkout, pagamentos, pedidos, frete e painel administrativo.

Tecnologias principais:

- PHP 8.2 e Laravel 12;
- MySQL em desenvolvimento/local;
- Blade, Tailwind CSS 4, Vite e Alpine.js;
- Asaas para Pix, boleto e cartão;
- Melhor Envio para serviços de frete e acompanhamento de envio.

## Organização de código

```text
app/
├── Actions/                 Operações pequenas e com uma responsabilidade
│   ├── Checkout/            Endereço, pedido, itens e envio
│   └── Payment/             Cobranças e processamento de webhook
├── Data/                    DTOs normalizados
│   └── CheckoutData.php
├── Exceptions/Domain/        Exceções de regra de negócio
├── Http/
│   ├── Controllers/         Entrada HTTP; não concentra regra de negócio
│   └── Requests/            Validação por área e recurso
├── Models/                  Entidades e relacionamentos Eloquent
├── Observers/               Regras automáticas de persistência
└── Services/                Orquestração e integrações externas
    ├── Admin/               Serviços administrativos por recurso
    ├── Public/              Fluxos exclusivos da loja
    └── Shipping/            Melhor Envio e mapeamento de status
database/
├── factories/               Dados aleatórios para testes
├── migrations/              Estrutura do banco
└── seeders/                 Catálogo e cenário de demonstração
resources/views/
├── components/              Componentes Blade reutilizáveis
├── layouts/                 Cabeçalho, rodapé e layouts
├── admin/                   Telas do painel administrativo
└── public/                  Telas da loja
tests/Feature/               Testes dos fluxos de negócio
```

### Convenções

- **Controller:** recebe a requisição, usa Request validado e devolve view/redirect/JSON.
- **Service:** coordena um fluxo ou encapsula uma integração, como Asaas e Melhor Envio.
- **Action:** executa uma única operação importante, reutilizável e simples de testar.
- **DTO (`Data`):** transforma dados de entrada em uma estrutura consistente.
- **Model:** representa a tabela e seus relacionamentos.
- **Policy:** centraliza a autorização sobre uma entidade, como pedido ou endereço.
- **Observer:** aplica regra de persistência automática, como a geração de slugs.

Não há uma dependência extra para Actions ou DTOs: são classes nativas PHP/Laravel, carregadas pelo autoload `App\`.

### Autorização

As Policies `OrderPolicy` e `AddressPolicy` garantem que um cliente só acesse ou altere os próprios dados. As consultas continuam filtradas por `user_id` antes da Policy para preservar a resposta 404 quando um pedido ou endereço de outro cliente é solicitado.

### Autenticação e sessões

A aplicação mantém dois guards de sessão independentes:

- `web`, para clientes da loja;
- `admin`, para administradores do painel.

O fluxo completo de login, limitação de tentativas, senhas fortes e recuperação de acesso está em [AUTENTICACAO.md](AUTENTICACAO.md).

As telas públicas de login e cadastro usam o mesmo layout, largura de card, tipografia, paleta rosé, campos e espaçamentos. O cadastro preserva os campos próprios de nome, e-mail, telefone e senha, mas funciona como continuação visual direta do login.

O middleware `RedirectAuthenticatedUser` protege os formulários exibidos apenas para visitantes e resolve o destino conforme o guard ativo:

| Rota acessada | Sessão autenticada | Destino |
|---|---|---|
| `/login` ou `/register` | Cliente (`web`) | Home pública (`home`) |
| `/login` ou `/register` | Administrador (`admin`) | Painel administrativo (`admin.dashboard`) |
| `/admin/login` | Administrador (`admin`) | Painel administrativo (`admin.dashboard`) |

Visitantes continuam acessando os formulários normalmente. O GET `/admin/login` verifica somente o guard `admin`, portanto uma sessão comum do guard `web` não impede o acesso ao login administrativo. Os POSTs de autenticação não recebem esse middleware e mantêm o fluxo dos controllers.

O botão **Sair** da barra lateral administrativa envia um formulário POST com proteção CSRF para `admin.logout`. O controller encerra especificamente o guard `admin`, rotaciona o identificador da sessão e regenera o token CSRF antes de redirecionar para `admin.login`. A sessão do guard `web`, quando existente no mesmo navegador, é preservada.

## Interface administrativa

O painel usa `layouts/admin/app.blade.php` como base comum e `layouts/admin/partials/sidebar.blade.php` para a navegação. A identidade visual administrativa mantém os tons escuros, rosé e off-white da Malu Store, com `Manrope` para a interface e `Cormorant Garamond` para títulos.

Telas já padronizadas:

- login do administrador;
- dashboard com indicadores de vendas gerais e do mês, pedidos pagos/pendentes e alerta de estoque baixo;
- listagens de produtos, pedidos, clientes, categorias, administradores e envios;
- detalhes, criação e edição de categorias;
- criação e edição de administradores;
- edição de envios.

No desktop, a barra lateral acompanha a rolagem e pode ser recolhida pelo botão no topo do conteúdo. Em telas menores, ela vira um menu sobreposto, aberto pelo botão **Menu**; tabelas usam rolagem horizontal para não perder colunas.

## Responsividade e layout público

O layout público usa a classe `.store-container`, que ocupa a largura disponível mantendo apenas uma margem lateral fluida (`clamp`). Não há largura máxima fixa nos containers públicos: header, home, footer e demais seções acompanham monitores maiores e diferentes níveis de zoom sem ficarem estreitos no centro.

- títulos, marca e textos estruturais do header/footer usam medidas fluidas com `clamp`;
- a home ajusta o banner, círculo de categorias, cabeçalho de novidades e grid de produtos para celular, tablet e desktop;
- o menu de categorias do header abre como menu sobreposto em telas menores;
- a navegação do painel administrativo vira menu recolhível no celular;
- tabelas administrativas têm rolagem horizontal quando a largura não comporta todas as colunas.

### Estrutura das views

As páginas continuam usando `@extends('layouts.admin.app')` e `@section('content')` para compor a tela dentro do layout. Esse padrão define a página, enquanto os componentes Blade são usados apenas para blocos repetidos.

Componentes administrativos já criados em `resources/views/components/admin/`:

| Componente | Responsabilidade |
|---|---|
| `<x-admin.page-header>` | Eyebrow, título, descrição e área opcional de ações da página. |
| `<x-admin.table-card>` | Contêiner visual da tabela com borda, sombra e rolagem horizontal. |

As páginas de **Produtos** e **Categorias** já utilizam esses componentes. Novas telas devem seguir o mesmo critério: extrair um componente quando o mesmo bloco tiver uso real em mais de uma página, sem transformar conteúdo específico em componente desnecessário.

## Páginas legais

As páginas públicas abaixo usam o componente reutilizável `resources/views/components/legal/document.blade.php`, que centraliza o cabeçalho e a leitura do conteúdo:

| Rota | View | Finalidade |
|---|---|---|
| `/policy` | `public/legal/policy.blade.php` | Política de troca e devolução. |
| `/terms` | `public/legal/terms.blade.php` | Termos de uso da loja. |
| `/privacy` | `public/legal/privacy.blade.php` | Política de privacidade. |

Os links estão disponíveis no rodapé público. Antes da publicação comercial, os textos devem ser revisados e complementados com os dados cadastrais corretos da empresa e validação jurídica adequada à operação.

## Banco de dados

O modelo completo de tabelas, relações, snapshots, seeders, imagens e comandos de banco está em [BANCO_DE_DADOS.md](BANCO_DE_DADOS.md).

## Fluxo de catálogo e produto

1. `HomeController` e `CatalogController` carregam produtos ativos com imagens e variantes.
2. Só produtos que têm variante com estoque positivo são exibidos.
3. A busca usa o nome do produto.
4. O catálogo filtra por categoria, tamanho, cor e preço.
5. Os cards usam `components/store/product-card.blade.php`.
6. O selo **Novo** aparece até 30 dias após `products.created_at`.

### Slugs de categoria e produto

`CategoryObserver` gera o slug da categoria quando ele não é informado. `ProductObserver` gera um slug único para cada produto. A página pública de produto usa `/produtos/{slug}`; links antigos por ID são redirecionados para a URL atual.

## Carrinho e favoritos

## Perfil do cliente

A página `/perfil` reúne os dados de conta, troca de senha e endereços. O topo apresenta um resumo com total de pedidos, favoritos e endereços, além de atalhos para pedidos e favoritos.

Cada cliente pode manter até 10 endereços. Ao atingir esse limite, o formulário de inclusão é substituído por uma orientação para remover um endereço existente.

Na página de um pedido, o endereço exibido é o snapshot salvo no próprio pedido. Assim, a alteração posterior do cadastro de endereço não muda o histórico da compra.

### Carrinho

`CartService` encontra ou cria um carrinho ativo do usuário. Ao adicionar uma variante já existente, a quantidade é incrementada; caso contrário, um `cart_item` é criado com snapshots do produto.

O indicador da sacola no cabeçalho soma as quantidades reais de itens do carrinho ativo. Carrinho e checkout exigem autenticação.

### Favoritos

O coração usa `FavoriteController` e a tabela `favorites`. O mesmo botão alterna entre adicionar e remover. A página `/favoritos` lista os produtos salvos pelo cliente autenticado.

## Checkout

O checkout é transacional: se uma etapa falhar, nenhuma parte parcial do pedido é mantida.

```text
CheckoutController
  → CheckoutService
    → CheckoutData (normaliza CPF, CEP, estado, complemento e frete)
    → ResolveAddressAction
    → CreateOrderAction
    → CreateOrderItemsAction
    → CreateShipmentAction
```

Detalhes:

1. O cliente informa endereço e seleciona frete.
2. `CheckoutData` normaliza os dados de entrada.
3. O endereço é criado ou atualizado.
4. Pedidos antigos ainda pendentes são cancelados.
5. O pedido é criado com totais e snapshot do endereço.
6. Itens do carrinho tornam-se `order_items`.
7. Um `shipment` pendente é criado.
8. O cliente é encaminhado à forma de pagamento.

## Pagamentos com Asaas

`PaymentService` cuida das telas e das respostas HTTP. A lógica de cada meio foi separada em Actions:

- `CreatePixPaymentAction`: cria cobrança Pix, busca QR Code e define expiração de 30 minutos.
- `CreateBoletoPaymentAction`: cria boleto e guarda a data de vencimento.
- `ProcessCardPaymentAction`: processa cartão e atualiza o pedido conforme a resposta.

Os dados sensíveis e URL da API devem permanecer no `.env`; nunca devem ser enviados ao repositório público.

## Webhook do Asaas e estoque

O endpoint recebe eventos em `routes/api.php`. A estrutura é:

```text
WebhookController
  → ProcessAsaasWebhook (Job na fila)
    → AsaasWebhookService
    → UpdateOrderFromAsaasWebhookAction
      → FinalizePaidOrderAction (somente na primeira confirmação)
```

Eventos tratados:

| Evento Asaas | Status interno |
|---|---|
| `PAYMENT_CREATED` | pendente |
| `PAYMENT_CONFIRMED` | pago |
| `PAYMENT_RECEIVED` | pago |
| `PAYMENT_OVERDUE` | vencido |
| `PAYMENT_DELETED` | cancelado |
| `PAYMENT_REFUNDED` | cancelado |

Quando o pagamento vira `paid` pela primeira vez, `FinalizePaidOrderAction` baixa o estoque das variantes e limpa o carrinho ativo. Se o Asaas enviar confirmação e recebimento para o mesmo pagamento, a baixa não se repete.

## Dados de demonstração

`php artisan db:seed` executa:

- `StoreCatalogSeeder`: categorias, produtos, fotos e variantes;
- `StoreDemoDataSeeder`: usuário, admin, endereço, pedidos, envios e carrinho de teste.

Credenciais locais de demonstração:

| Perfil | E-mail | Senha |
|---|---|---|
| Cliente | `test@gmail.com` | `Senha@2026` |
| Administrador | `admin@malustore.test` | `Senha@2026` |

Essas credenciais são apenas para ambiente local.

Após incluir uma migration nova em uma instalação existente, execute `php artisan migrate`. Para apenas reaplicar os dados de demonstração sem apagar tabelas, use `php artisan db:seed`.

## Testes

Execute todos os testes:

```bash
php artisan test
```

Cobertura atual:

- home responde corretamente;
- checkout cria endereço, pedido, itens e envio;
- carrinho adiciona, atualiza e remove item;
- favoritos adicionam e removem produto;
- webhook recebido baixa o estoque somente uma vez e limpa o carrinho.
- rotas de cliente exigem autenticação e pedidos não podem ser acessados por outro usuário.
- catálogo filtra busca/categoria e oculta produtos inativos ou sem estoque.
- slugs de categorias e produtos são únicos e as URLs antigas de produto redirecionam corretamente;
- status de envio do provedor é convertido em um único ponto de mapeamento.

## Observabilidade

Os eventos críticos são registrados em `storage/logs/laravel.log` com contexto técnico mínimo e sem registrar credenciais, dados de cartão, QR Code, payload completo de webhook ou dados pessoais completos:

- checkout concluído;
- criação de cobrança Pix e boleto;
- processamento de cobrança por cartão;
- erro ao consultar opções de frete;
- falha definitiva de uma Job de webhook do Asaas, após as tentativas configuradas.

Com `ALERT_EMAIL` configurado no ambiente, falhas críticas do webhook do Asaas, da criação de cobranças e da consulta de frete enviam um e-mail operacional com contexto técnico mínimo. O canal de e-mail não registra credenciais, dados de cartão, QR Code, payload completo de webhook ou dados pessoais completos. O procedimento de SMTP e o comando de teste estão em [IMPLANTACAO.md](IMPLANTACAO.md).

## Comandos de desenvolvimento

```bash
npm run dev
php artisan serve
php artisan migrate
php artisan db:seed
php artisan test
npm run build
php artisan optimize:clear
```

## Próximas melhorias recomendadas

1. Criar testes com `Http::fake()` para Pix, boleto e cartão, sem chamar a API real.
2. Criar testes de autorização para impedir que um usuário consulte pedido, carrinho ou favorito de outro.
3. Ampliar as Actions de frete para a geração e sincronização de etiquetas.
4. Colocar também a sincronização de etiquetas e outros serviços externos em fila.
5. Definir e configurar um canal de alertas de produção para os logs críticos.
