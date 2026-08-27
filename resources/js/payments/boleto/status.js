import { createPaymentStatusPoller } from '../shared/payment-status-poller';

export function initBoletoStatus() {
    const statusElement = document.getElementById('boletoStatus');

    return createPaymentStatusPoller({
        statusUrl: window.BOLETO_STATUS_URL,
        successUrl: window.BOLETO_SUCCESS_URL,
        errorUrl: window.BOLETO_ERROR_URL,
        onPending: () => {
            if (statusElement) {
                statusElement.textContent = 'Aguardando confirmação do pagamento.';
            }
        },
        onPaid: () => {
            if (statusElement) {
                statusElement.textContent = 'Pagamento confirmado. Redirecionando...';
            }
        },
    });
}
