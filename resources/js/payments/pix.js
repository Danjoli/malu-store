document.addEventListener('DOMContentLoaded', () => {

    const countdownElement = document.getElementById('countdown');
    const pixCodeElement = document.getElementById('pixCode');
    const copyButton = document.getElementById('copyPixButton');

    /*
    |--------------------------------------------------------------------------
    | Copiar PIX Copia e Cola
    |--------------------------------------------------------------------------
    */

    if (copyButton && pixCodeElement) {

        copyButton.addEventListener('click', async () => {

            const pixCode = pixCodeElement.value.trim();

            if (!pixCode) {
                alert('Código PIX não encontrado.');
                return;
            }

            try {

                /*
                |--------------------------------------------------------------------------
                | Método moderno
                |--------------------------------------------------------------------------
                */

                if (
                    navigator.clipboard &&
                    window.isSecureContext
                ) {

                    await navigator.clipboard.writeText(pixCode);

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Fallback para navegadores que não suportam Clipboard API
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

                }

                /*
                |--------------------------------------------------------------------------
                | Feedback visual
                |--------------------------------------------------------------------------
                */

                const originalText =
                    copyButton.textContent;

                copyButton.textContent =
                    'PIX copiado!';

                copyButton.classList.remove(
                    'bg-green-500'
                );

                copyButton.classList.add(
                    'bg-green-700'
                );

                setTimeout(() => {

                    copyButton.textContent =
                        originalText;

                    copyButton.classList.remove(
                        'bg-green-700'
                    );

                    copyButton.classList.add(
                        'bg-green-500'
                    );

                }, 2000);


            } catch (error) {

                console.error(
                    'Erro ao copiar código PIX:',
                    error
                );

                /*
                |--------------------------------------------------------------------------
                | Última tentativa: seleciona o código
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
