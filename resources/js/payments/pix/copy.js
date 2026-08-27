export function initPixCopy() {
    const codeElement = document.getElementById('pixCode');
    const button = document.querySelector('[data-copy-pix]');

    if (!codeElement || !button) {
        return;
    }

    button.addEventListener('click', async () => {
        const code = codeElement.value.trim();

        if (!code) {
            window.alert('Código Pix não encontrado.');
            return;
        }

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(code);
            } else {
                codeElement.focus();
                codeElement.select();
                codeElement.setSelectionRange(0, code.length);

                if (!document.execCommand('copy')) {
                    throw new Error('Não foi possível copiar o código Pix.');
                }
            }

            window.alert('Código Pix copiado.');
        } catch (error) {
            console.error('Não foi possível copiar o código Pix.', error);
            codeElement.focus();
            codeElement.select();
            window.alert('Não foi possível copiar automaticamente. Pressione Ctrl+C para copiar.');
        }
    });
}
