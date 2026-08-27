const FAILED_STATUSES = new Set(['cancelled', 'expired', 'failed']);

/**
 * Consulta o status de uma cobrança e redireciona quando ela é concluída.
 */
export function createPaymentStatusPoller({
    statusUrl,
    successUrl,
    errorUrl,
    interval = 5000,
    onPending = () => {},
    onPaid = () => {},
    onFailed = () => {},
}) {
    let intervalId = null;
    let checking = false;

    const stop = () => {
        if (intervalId) {
            window.clearInterval(intervalId);
            intervalId = null;
        }
    };

    const check = async () => {
        if (checking) {
            return;
        }

        checking = true;

        try {
            const response = await fetch(statusUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                cache: 'no-store',
            });

            if (!response.ok) {
                throw new Error(`Erro HTTP ${response.status}`);
            }

            const { status } = await response.json();

            if (status === 'paid') {
                stop();
                onPaid();
                window.location.assign(successUrl);
                return;
            }

            if (FAILED_STATUSES.has(status)) {
                stop();
                onFailed();
                window.location.assign(errorUrl);
                return;
            }

            onPending(status);
        } catch (error) {
            console.error('Não foi possível consultar o status do pagamento.', error);
        } finally {
            checking = false;
        }
    };

    if (!statusUrl || !successUrl || !errorUrl) {
        console.error('URLs de status do pagamento não foram configuradas.');

        return { stop };
    }

    check();
    intervalId = window.setInterval(check, interval);
    window.addEventListener('beforeunload', stop, { once: true });

    return { stop };
}
