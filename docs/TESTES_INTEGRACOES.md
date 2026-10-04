# Testes das integrações externas

Os testes automatizados nunca chamam Asaas ou Melhor Envio de verdade. `Http::fake()` intercepta todas as requisições e as respostas representativas ficam em `tests/Fixtures`.

## Cenários cobertos

- sucesso e contrato mínimo das respostas;
- timeout ou falha de conexão, tratado como falha operacional transitória;
- erro HTTP definitivo (validação) e erro HTTP transitório;
- corpo JSON inválido;
- repetição de webhook sem duplicar baixa de estoque;
- repetição de atualização de envio sem criar outro registro.
- criação do Checkout hospedado sem enviar número, validade ou CVV à API pelo backend da loja;
- eventos de Checkout pago, cancelado e expirado.

Use apenas identificadores, documentos e credenciais fictícios. Para executar: `php artisan test --testsuite=Feature`.
