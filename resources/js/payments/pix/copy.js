export function initPixCopy() {

    const pixCodeElement =
        document.getElementById('pixCode');

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
            | Fallback
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
}
