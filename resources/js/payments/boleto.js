document.addEventListener("DOMContentLoaded", () => {

    const orderId = window.BOLETO_ORDER_ID;

    const checkStatus = async () => {

        try {
            const response = await fetch(window.BOLETO_STATUS_URL);
            const data = await response.json();

            if (data.status === 'expired') {
                window.location.href = window.BOLETO_ERROR_URL;
            }

            if (data.status === 'paid') {
                window.location.href = window.BOLETO_SUCCESS_URL;
            }

        } catch (e) {
            console.error('Erro ao verificar status:', e);
        }
    };

    setInterval(checkStatus, 5000);
});
