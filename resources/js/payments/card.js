document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('cardPaymentForm');
    const button = document.getElementById('cardSubmitButton');
    const errorElement = document.getElementById('cardError');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        // Esconde o erro antigo do formulário, caso exista
        if (errorElement) {
            errorElement.classList.add('hidden');
            errorElement.textContent = '';
        }

        // Desabilita o botão durante o processamento
        button.disabled = true;
        button.textContent = 'Processando pagamento...';

        // Coleta os dados do formulário
        const formData = new FormData(form);

        try {

            const response = await fetch(
                window.CARD_PAYMENT_URL,
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token')
                    },

                    body: formData
                }
            );

            const data = await response.json();

            console.log('Resposta do servidor:', data);

            /*
            |--------------------------------------------------------------------------
            | ERROS HTTP
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                // Erros de validação do ProcessCardPaymentRequest
                if (data.errors) {

                    const validationErrors = Object.values(data.errors)
                        .flat()
                        .join('<br>');

                    await Swal.fire({
                        icon: 'error',
                        title: 'Erro de validação',
                        html: validationErrors,
                        confirmButtonColor: '#dc2626'
                    });

                    button.disabled = false;
                    button.textContent = 'Pagar com Cartão';

                    return;
                }

                // Outros erros retornados pela API
                await Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: data.message ||
                        'Não foi possível processar o pagamento.',
                    confirmButtonColor: '#dc2626'
                });

                button.disabled = false;
                button.textContent = 'Pagar com Cartão';

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | PAGAMENTO PROCESSADO
            |--------------------------------------------------------------------------
            */

            if (data.success) {

                window.location.href =
                    window.CARD_SUCCESS_URL;

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPOSTA INESPERADA
            |--------------------------------------------------------------------------
            */

            await Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'O pagamento não foi processado.',
                confirmButtonColor: '#dc2626'
            });

            button.disabled = false;
            button.textContent = 'Pagar com Cartão';

        } catch (error) {

            console.error(
                'Erro no pagamento:',
                error
            );

            /*
            |--------------------------------------------------------------------------
            | ERRO INESPERADO
            |--------------------------------------------------------------------------
            */

            await Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: error.message ||
                    'Erro ao processar o pagamento.',
                confirmButtonColor: '#dc2626'
            });

            button.disabled = false;
            button.textContent = 'Pagar com Cartão';

        }

    });

});
