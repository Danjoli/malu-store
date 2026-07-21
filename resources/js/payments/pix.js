console.log('PIX JS carregado');

document.addEventListener('DOMContentLoaded', () => {

    console.log('DOM carregado');

    const countdownElement = document.getElementById('countdown');
    const pixCodeElement = document.getElementById('pixCode');
    const copyButton = document.getElementById('copyPixButton');

    console.log({
        pixCodeElement,
        copyButton
    });


    /*
    |--------------------------------------------------------------------------
    | Copiar PIX Copia e Cola
    |--------------------------------------------------------------------------
    */

    if (pixCodeElement && copyButton) {

        copyButton.addEventListener('click', function () {

            pixCodeElement.select();
            pixCodeElement.setSelectionRange(0, 99999);


            try {

                document.execCommand('copy');

                copyButton.innerHTML = 'PIX copiado!';


                setTimeout(() => {

                    copyButton.innerHTML =
                        'Copiar código PIX';

                }, 2000);


            } catch (error) {

                console.error(
                    'Erro ao copiar:',
                    error
                );

                alert(
                    'Erro ao copiar o PIX'
                );

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | Timer de expiração do PIX
    |--------------------------------------------------------------------------
    */

    const expiresAt = window.PIX_EXPIRES_AT;


    if (countdownElement && expiresAt) {


        const expirationTime =
            Date.parse(expiresAt);



        if (!isNaN(expirationTime)) {


            const updateCountdown = () => {


                const distance =
                    expirationTime - Date.now();



                if (distance <= 0) {


                    countdownElement.textContent =
                        '00:00';



                    clearInterval(
                        countdownInterval
                    );


                    /*
                    Aqui você pode futuramente
                    redirecionar para pagamento expirado
                    */


                    return;

                }



                const minutes = Math.floor(
                    distance / 60000
                );



                const seconds = Math.floor(
                    (distance % 60000) / 1000
                );



                countdownElement.textContent =
                    `${String(minutes).padStart(2, '0')}:` +
                    `${String(seconds).padStart(2, '0')}`;


            };



            // Atualiza imediatamente
            updateCountdown();



            // Atualiza a cada segundo
            const countdownInterval =
                setInterval(
                    updateCountdown,
                    1000
                );



            window.addEventListener(
                'beforeunload',
                () => {

                    clearInterval(
                        countdownInterval
                    );

                }
            );


        } else {

            console.error(
                'PIX_EXPIRES_AT inválido:',
                expiresAt
            );

        }

    }

});
