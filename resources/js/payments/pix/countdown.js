export function initPixCountdown() {

    const countdownElement =
        document.getElementById('countdown');

    const expiresAt =
        window.PIX_EXPIRES_AT;

    let countdownInterval = null;

    if (
        !countdownElement ||
        !expiresAt
    ) {
        return {
            stop: () => {}
        };
    }

    const expirationTime =
        Date.parse(expiresAt);

    if (isNaN(expirationTime)) {

        console.error(
            'PIX_EXPIRES_AT inválido:',
            expiresAt
        );

        return {
            stop: () => {}
        };
    }

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

            stop();

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

    const stop = () => {

        if (countdownInterval) {

            clearInterval(
                countdownInterval
            );

            countdownInterval = null;
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Primeira atualização
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

    return {
        stop
    };
}
