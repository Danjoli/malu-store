import { createPaymentStatusPoller } from '../shared/payment-status-poller';

export function initPixStatus({ onPaid, onFailed }) {
    return createPaymentStatusPoller({
        statusUrl: window.PIX_STATUS_URL,
        successUrl: window.PIX_SUCCESS_URL,
        errorUrl: window.PIX_ERROR_URL,
        onPaid,
        onFailed,
    });
}
