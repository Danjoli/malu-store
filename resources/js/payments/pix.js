document.addEventListener('DOMContentLoaded', () => {
    const countdownElement = document.getElementById('countdown');
    const pixCodeElement = document.getElementById('pixCode');
    const copyButton = document.getElementById('copyPixButton');

    /*
    |--------------------------------------------------------------------------
    | Configurações vindas do Blade
    |--------------------------------------------------------------------------
    */

    const expiresAt = window.PIX_EXPIRES_AT;
    const paymentStatusUrl = window.PIX_STATUS_URL;
    const paymentErrorUrl = window.PIX_ERROR_URL;
    const paymentSuccessUrl = window.PIX_SUCCESS_URL;

    /*
    |--------------------------------------------------------------------------
    | Copiar PIX Copia e Cola
    |--------------------------------------------------------------------------
    */

    if (copyButton && pixCodeElement) {
        copyButton.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(
                    pixCodeElement.value
                );

                copyButton.textContent = 'PIX copiado!';

                setTimeout(() => {
                    copyButton.textContent = 'Copiar código PIX';
                }, 2000);

            } catch (error) {
                console.error(
                    'Erro ao copiar código PIX:',
                    error
                );

                alert(
                    'Não foi possível copiar o código PIX.'
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Contador de expiração
    |--------------------------------------------------------------------------
    */

    if (countdownElement && expiresAt) {
        const expirationTime = new Date(expiresAt).getTime();

        const countdown = setInterval(() => {
            const now = new Date().getTime();

            const distance = expirationTime - now;

            if (distance <= 0) {
                clearInterval(countdown);

                countdownElement.textContent = '00:00';

                if (paymentErrorUrl) {
                    window.location.href = paymentErrorUrl;
                }

                return;
            }

            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) /
                (1000 * 60)
            );

            const seconds = Math.floor(
                (distance % (1000 * 60)) /
                1000
            );

            countdownElement.textContent =
                `${String(minutes).padStart(2, '0')}:` +
                `${String(seconds).padStart(2, '0')}`;
        }, 1000);
    }

    /*
    |--------------------------------------------------------------------------
    | Verificar status do pagamento
    |--------------------------------------------------------------------------
    */

    if (paymentStatusUrl) {
        const checkPaymentStatus = async () => {
            try {
                const response = await fetch(
                    paymentStatusUrl,
                    {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        `HTTP ${response.status}`
                    );
                }

                const data = await response.json();

                /*
                |--------------------------------------------------------------------------
                | Pagamento aprovado
                |--------------------------------------------------------------------------
                */

                if (
                    data.status === 'paid' ||
                    data.status === 'confirmed' ||
                    data.status === 'received'
                ) {
                    if (paymentSuccessUrl) {
                        window.location.href =
                            paymentSuccessUrl;
                    }

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Pagamento expirado
                |--------------------------------------------------------------------------
                */

                if (
                    data.status === 'expired' ||
                    data.status === 'overdue'
                ) {
                    if (paymentErrorUrl) {
                        window.location.href =
                            paymentErrorUrl;
                    }

                    return;
                }

            } catch (error) {
                console.error(
                    'Erro ao verificar pagamento:',
                    error
                );
            }
        };

        /*
        | Verifica imediatamente
        */

        checkPaymentStatus();

        /*
        | Verifica a cada 5 segundos
        */

        const paymentInterval = setInterval(
            checkPaymentStatus,
            5000
        );

        /*
        | Limpa o intervalo quando sair da página
        */

        window.addEventListener(
            'beforeunload',
            () => {
                clearInterval(paymentInterval);
            }
        );
    }
});
