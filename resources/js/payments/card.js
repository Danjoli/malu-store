document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('cardPaymentForm');
    const button = document.getElementById('cardSubmitButton');
    const errorElement = document.getElementById('cardError');

    if (!form) {
        return;
    }

    form.addEventListener('submit', async (event) => {

        event.preventDefault();

        errorElement.classList.add('hidden');
        errorElement.textContent = '';

        button.disabled = true;
        button.textContent = 'Processando pagamento...';

        const formData = new FormData(form);

        try {

            const response = await fetch(
                window.CARD_PAYMENT_URL,
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('input[name="_token"]')
                            .value
                    },

                    body: formData
                }
            );

            const data = await response.json();

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Não foi possível processar o pagamento.'
                );

            }

            if (data.success) {

                window.location.href =
                    window.CARD_SUCCESS_URL;

                return;
            }

            throw new Error(
                'O pagamento não foi processado.'
            );

        } catch (error) {

            console.error(
                'Erro no pagamento:',
                error
            );

            errorElement.textContent =
                error.message ||
                'Erro ao processar o pagamento.';

            errorElement.classList.remove('hidden');

            button.disabled = false;
            button.textContent = 'Pagar com Cartão';

        }

    });

});
