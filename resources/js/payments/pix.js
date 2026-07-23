document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Elementos da página
    |--------------------------------------------------------------------------
    */

    const countdownElement =
        document.getElementById('countdown');

    const pixCodeElement =
        document.getElementById('pixCode');


    /*
    |--------------------------------------------------------------------------
    | Variáveis dos intervalos
    |--------------------------------------------------------------------------
    */

    let countdownInterval = null;

    let statusInterval = null;


    /*
    |--------------------------------------------------------------------------
    | Copiar PIX Copia e Cola
    |--------------------------------------------------------------------------
    */

    window.copiarPix = async function () {

        if (!pixCodeElement) {

            alert(
                'Código PIX não encontrado.'
            );

            return;
        }


        const pixCode =
            pixCodeElement.value.trim();


        if (!pixCode) {

            alert(
                'Código PIX não encontrado.'
            );

            return;
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Clipboard API
            |--------------------------------------------------------------------------
            */

            if (
                navigator.clipboard &&
                window.isSecureContext
            ) {

                await navigator.clipboard.writeText(
                    pixCode
                );

                alert(
                    'Código PIX copiado!'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Fallback para navegadores sem Clipboard API
            |--------------------------------------------------------------------------
            */

            pixCodeElement.focus();

            pixCodeElement.select();

            pixCodeElement.setSelectionRange(
                0,
                pixCode.length
            );


            const copied =
                document.execCommand('copy');


            if (!copied) {

                throw new Error(
                    'Não foi possível copiar o código PIX.'
                );

            }


            alert(
                'Código PIX copiado!'
            );


        } catch (error) {

            console.error(
                'Erro ao copiar código PIX:',
                error
            );


            /*
            |--------------------------------------------------------------------------
            | Última tentativa
            |--------------------------------------------------------------------------
            */

            pixCodeElement.focus();

            pixCodeElement.select();

            pixCodeElement.setSelectionRange(
                0,
                pixCodeElement.value.length
            );


            alert(
                'Não foi possível copiar automaticamente. ' +
                'O código PIX foi selecionado. ' +
                'Pressione Ctrl+C para copiar.'
            );

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Timer de expiração do PIX
    |--------------------------------------------------------------------------
    */

    const expiresAt =
        window.PIX_EXPIRES_AT;


    if (
        countdownElement &&
        expiresAt
    ) {

        const expirationTime =
            Date.parse(expiresAt);


        if (!isNaN(expirationTime)) {

            const updateCountdown = () => {

                const distance =
                    expirationTime - Date.now();


                /*
                |--------------------------------------------------------------------------
                | PIX expirado
                |--------------------------------------------------------------------------
                */

                if (distance <= 0) {

                    countdownElement.textContent =
                        '00:00';


                    if (countdownInterval) {

                        clearInterval(
                            countdownInterval
                        );

                        countdownInterval = null;

                    }


                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Calcula minutos
                |--------------------------------------------------------------------------
                */

                const minutes =
                    Math.floor(
                        distance / 60000
                    );


                /*
                |--------------------------------------------------------------------------
                | Calcula segundos
                |--------------------------------------------------------------------------
                */

                const seconds =
                    Math.floor(
                        (distance % 60000) / 1000
                    );


                /*
                |--------------------------------------------------------------------------
                | Atualiza contador
                |--------------------------------------------------------------------------
                */

                countdownElement.textContent =
                    `${String(minutes).padStart(2, '0')}:` +
                    `${String(seconds).padStart(2, '0')}`;

            };


            /*
            |--------------------------------------------------------------------------
            | Atualiza imediatamente
            |--------------------------------------------------------------------------
            */

            updateCountdown();


            /*
            |--------------------------------------------------------------------------
            | Atualiza a cada segundo
            |--------------------------------------------------------------------------
            */

            countdownInterval =
                setInterval(
                    updateCountdown,
                    1000
                );


        } else {

            console.error(
                'PIX_EXPIRES_AT inválido:',
                expiresAt
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | DADOS DO PAGAMENTO
    |--------------------------------------------------------------------------
    */

    const orderId =
        window.PIX_ORDER_ID;


    const statusUrl =
        window.PIX_STATUS_URL;


    const successUrl =
        window.PIX_SUCCESS_URL;


    const errorUrl =
        window.PIX_ERROR_URL;


    /*
    |--------------------------------------------------------------------------
    | Verifica se os dados necessários existem
    |--------------------------------------------------------------------------
    */

    if (
        !orderId ||
        !statusUrl ||
        !successUrl ||
        !errorUrl
    ) {

        console.error(
            'Dados necessários para consultar status do PIX não encontrados.',
            {
                orderId,
                statusUrl,
                successUrl,
                errorUrl
            }
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Evita múltiplas consultas simultâneas
    |--------------------------------------------------------------------------
    */

    let checkingStatus = false;


    /*
    |--------------------------------------------------------------------------
    | Consulta status do pagamento
    |--------------------------------------------------------------------------
    */

    const checkPaymentStatus = async () => {

        /*
        |--------------------------------------------------------------------------
        | Evita duas requisições ao mesmo tempo
        |--------------------------------------------------------------------------
        */

        if (checkingStatus) {

            return;
        }


        checkingStatus = true;


        try {

            const response =
                await fetch(
                    statusUrl,
                    {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },

                        cache: 'no-store'
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Verifica erro HTTP
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    `Erro HTTP ${response.status}`
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Converte resposta para JSON
            |--------------------------------------------------------------------------
            */

            const data =
                await response.json();


            console.log(
                'Status atual do PIX:',
                data
            );


            const status =
                data.status;


            /*
            |--------------------------------------------------------------------------
            | PAGAMENTO APROVADO
            |--------------------------------------------------------------------------
            */

            if (status === 'paid') {

                console.log(
                    'PIX pago. Redirecionando para página de sucesso.'
                );


                /*
                |--------------------------------------------------------------------------
                | Para o monitoramento do status
                |--------------------------------------------------------------------------
                */

                if (statusInterval) {

                    clearInterval(
                        statusInterval
                    );

                    statusInterval = null;

                }


                /*
                |--------------------------------------------------------------------------
                | Para o contador
                |--------------------------------------------------------------------------
                */

                if (countdownInterval) {

                    clearInterval(
                        countdownInterval
                    );

                    countdownInterval = null;

                }


                /*
                |--------------------------------------------------------------------------
                | Redireciona para sucesso
                |--------------------------------------------------------------------------
                */

                window.location.href =
                    successUrl;


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PAGAMENTO COM ERRO
            |--------------------------------------------------------------------------
            */

            if (
                status === 'cancelled' ||
                status === 'expired' ||
                status === 'failed'
            ) {

                console.log(
                    'PIX não concluído. Redirecionando para página de erro.'
                );


                /*
                |--------------------------------------------------------------------------
                | Para o monitoramento do status
                |--------------------------------------------------------------------------
                */

                if (statusInterval) {

                    clearInterval(
                        statusInterval
                    );

                    statusInterval = null;

                }


                /*
                |--------------------------------------------------------------------------
                | Para o contador
                |--------------------------------------------------------------------------
                */

                if (countdownInterval) {

                    clearInterval(
                        countdownInterval
                    );

                    countdownInterval = null;

                }


                /*
                |--------------------------------------------------------------------------
                | Redireciona para erro
                |--------------------------------------------------------------------------
                */

                window.location.href =
                    errorUrl;


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | PAGAMENTO PENDENTE
            |--------------------------------------------------------------------------
            |
            | Não faz nada.
            | A próxima consulta será feita em 5 segundos.
            |
            |--------------------------------------------------------------------------
            */

        } catch (error) {

            console.error(
                'Erro ao consultar status do PIX:',
                error
            );

        } finally {

            checkingStatus = false;

        }

    };


    /*
    |--------------------------------------------------------------------------
    | Primeira consulta
    |--------------------------------------------------------------------------
    */

    checkPaymentStatus();


    /*
    |--------------------------------------------------------------------------
    | Consulta a cada 5 segundos
    |--------------------------------------------------------------------------
    */

    statusInterval =
        setInterval(
            checkPaymentStatus,
            5000
        );


    /*
    |--------------------------------------------------------------------------
    | Limpa intervalos ao sair da página
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        () => {

            if (statusInterval) {

                clearInterval(
                    statusInterval
                );

                statusInterval = null;

            }


            if (countdownInterval) {

                clearInterval(
                    countdownInterval
                );

                countdownInterval = null;

            }

        }
    );

});
