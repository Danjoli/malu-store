export function initPixStatus({
    onPaid,
    onFailed
}) {

    const statusUrl =
        window.PIX_STATUS_URL;

    const successUrl =
        window.PIX_SUCCESS_URL;

    const errorUrl =
        window.PIX_ERROR_URL;

    let statusInterval = null;

    let checkingStatus = false;

    /*
    |--------------------------------------------------------------------------
    | Verifica dados necessários
    |--------------------------------------------------------------------------
    */

    if (
        !statusUrl ||
        !successUrl ||
        !errorUrl
    ) {

        console.error(
            'Dados necessários para consultar status do PIX não encontrados.',
            {
                statusUrl,
                successUrl,
                errorUrl
            }
        );

        return {
            stop: () => {}
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Para monitoramento
    |--------------------------------------------------------------------------
    */

    const stop = () => {

        if (statusInterval) {

            clearInterval(
                statusInterval
            );

            statusInterval = null;
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Consulta status
    |--------------------------------------------------------------------------
    */

    const checkPaymentStatus = async () => {

        /*
        |--------------------------------------------------------------------------
        | Evita requisições simultâneas
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

                stop();

                if (onPaid) {
                    onPaid();
                }

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

                stop();

                if (onFailed) {
                    onFailed();
                }

                window.location.href =
                    errorUrl;

                return;
            }

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
    | Limpa intervalo ao sair
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        stop
    );

    return {
        stop
    };
}
