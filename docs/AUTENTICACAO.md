# Autenticação e contas

## Áreas independentes

A aplicação mantém duas áreas autenticadas, com sessões e recuperação de senha separadas:

| Área | Guard | Login | Recuperação de senha |
|---|---|---|---|
| Cliente | `web` | `/login` | `/esqueci-a-senha` |
| Administração | `admin` | `/admin/login` | `/admin/esqueci-a-senha` |

Um cliente autenticado não ganha acesso administrativo, e uma sessão administrativa não substitui a sessão do cliente.

## Senhas

Cadastro, alteração de senha, recuperação de senha e criação de administradores exigem:

- no mínimo 8 caracteres;
- letra maiúscula e minúscula;
- ao menos um número;
- ao menos um símbolo.

Os formulários de login limitam tentativas consecutivas para reduzir ataques de força bruta. Contas administrativas inativas não podem entrar no painel.

## Recuperação de senha

Clientes usam a tabela `password_reset_tokens`; administradores usam `admin_password_reset_tokens`. Essa separação evita que tokens de uma área sejam aceitos na outra.

O sistema responde com a mesma mensagem para e-mails existentes ou inexistentes. Isso evita revelar se um endereço possui conta cadastrada.

Para o envio de e-mails funcionar, configure o SMTP no `.env` e limpe o cache de configuração:

```bash
php artisan optimize:clear
```

Os links de recuperação expiram em 60 minutos e o reenvio é limitado a uma solicitação por minuto.
