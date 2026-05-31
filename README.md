# Malu Store

Sistema de e-commerce desenvolvido em Laravel para gerenciamento e venda de produtos online.

## Sobre o Projeto

A Malu Store é uma plataforma de comércio eletrônico que permite o gerenciamento completo de produtos, pedidos, clientes e envios através de uma área administrativa dedicada.

O projeto foi desenvolvido utilizando Laravel seguindo boas práticas de organização, modularização e arquitetura MVC.

## Funcionalidades

### Área do Cliente

* Catálogo de produtos
* Visualização de detalhes dos produtos
* Carrinho de compras
* Checkout
* Acompanhamento de pedidos
* Sistema de autenticação de usuários

### Área Administrativa

* Dashboard administrativo
* Gerenciamento de produtos
* Gerenciamento de categorias
* Gerenciamento de administradores
* Gerenciamento de pedidos
* Controle de estoque
* Controle de envios
* Integração com serviços de frete

## Tecnologias Utilizadas

### Backend

* PHP 8+
* Laravel
* MySQL

### Frontend

* Blade
* HTML5
* CSS3
* JavaScript
* Tailwind CSS
* Alpine.js

### Ferramentas

* Composer
* NPM
* Vite
* Git

## Estrutura do Projeto

```text
malu_store/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── vendor/
├── artisan
├── composer.json
├── package.json
└── README.md
```

## Requisitos

* PHP 8.2 ou superior
* Composer
* Node.js
* NPM
* MySQL

## Instalação

Clone o projeto:

```bash
git clone URL_DO_REPOSITORIO
```

Entre na pasta do projeto:

```bash
cd malu_store
```

Instale as dependências PHP:

```bash
composer install
```

Instale as dependências JavaScript:

```bash
npm install
```

Crie o arquivo de ambiente:

```bash
cp .env.example .env
```

Gere a chave da aplicação:

```bash
php artisan key:generate
```

Configure o banco de dados no arquivo `.env`.

Execute as migrations:

```bash
php artisan migrate
```

Execute os seeders (caso existam):

```bash
php artisan db:seed
```

Inicie o servidor Laravel:

```bash
php artisan serve
```

Inicie o Vite:

```bash
npm run dev
```

## Comandos Úteis

Executar testes:

```bash
php artisan test
```

Executar migrations:

```bash
php artisan migrate
```

Limpar cache:

```bash
php artisan optimize:clear
```

Criar Controller:

```bash
php artisan make:controller NomeController
```

Criar Model:

```bash
php artisan make:model NomeModel -m
```

## Status do Projeto

Projeto em desenvolvimento contínuo.

Novas funcionalidades estão sendo implementadas conforme a evolução da plataforma.

## Autor

Desenvolvido por Danilo de Lima Fiod.
