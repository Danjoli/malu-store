document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Elemento de status
    |--------------------------------------------------------------------------
    */

    const statusElement =
        document.getElementById('boletoStatus');


    /*
    |--------------------------------------------------------------------------
    | Dados do pagamento
    |--------------------------------------------------------------------------
    */

    const orderId =
        window.BOLETO_ORDER_ID;

    const statusUrl =
        window.BOLETO_STATUS_URL;

    const successUrl =
        window.BOLETO_SUCCESS_URL;

    const errorUrl =
        window.BOLETO_ERROR_URL;


    /*
    |--------------------------------------------------------------------------
    | Verifica dados necessários
    |--------------------------------------------------------------------------
    */

    if (
        !orderId ||
        !statusUrl ||
        !successUrl ||
        !errorUrl
    ) {

        console.error(
            'Dados necessários para consultar o status do boleto não encontrados.',
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
    | Evita múltiplas requisições simultâneas
    |--------------------------------------------------------------------------
    */

    let checkingStatus = false;


    /*
    |--------------------------------------------------------------------------
    | Intervalo de consulta
    |--------------------------------------------------------------------------
    */

    let statusInterval = null;


    /*
    |--------------------------------------------------------------------------
    | Consulta status do pagamento
    |--------------------------------------------------------------------------
    */

    const checkPaymentStatus = async () => {

        /*
        |--------------------------------------------------------------------------
        | Evita consultas simultâneas
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
            | Converte resposta
            |--------------------------------------------------------------------------
            */

            const data =
                await response.json();


            console.log(
                'Status atual do boleto:',
                data
            );


            const status =
                data.status;


            /*
            |--------------------------------------------------------------------------
            | PAGAMENTO CONFIRMADO
            |--------------------------------------------------------------------------
            */

            if (status === 'paid') {

                console.log(
                    'Boleto pago. Redirecionando para página de sucesso.'
                );


                /*
                |--------------------------------------------------------------------------
                | Para consultas
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
                | Atualiza mensagem
                |--------------------------------------------------------------------------
                */

                if (statusElement) {

                    statusElement.innerHTML = `
                        <p class="text-green-600 font-semibold">
                            Pagamento confirmado!
                        </p>

                        <p class="text-sm text-gray-500 mt-2">
                            Redirecionando...
                        </p>
                    `;

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
                    'Boleto não concluído. Redirecionando para página de erro.'
                );


                /*
                |--------------------------------------------------------------------------
                | Para consultas
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
            */

            if (statusElement) {

                statusElement.innerHTML = `
                    <p class="text-gray-600">
                        Aguardando confirmação do pagamento...
                    </p>

                    <p class="text-sm text-gray-400 mt-2">
                        O status será atualizado automaticamente.
                    </p>
                `;

            }


        } catch (error) {

            console.error(
                'Erro ao consultar status do boleto:',
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
    | Limpa intervalo ao sair da página
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

        }
    );

});
