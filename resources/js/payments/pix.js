document.addEventListener("DOMContentLoaded", () => {

    const orderId = window.PIX_ORDER_ID;

    const expiresAt = new Date(window.PIX_EXPIRES_AT).getTime();

    const countdownEl = document.getElementById('countdown');

    // COPIAR PIX
    window.copiarPix = function () {
        const textarea = document.getElementById('pixCode');

        textarea.select();
        textarea.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(textarea.value);

        alert("Código PIX copiado!");
    };

    // CONTADOR
    const updateCountdown = () => {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance <= 0) {
            countdownEl.innerText = "00:00";
            window.location.href = `/payment-error/${orderId}?reason=expired`;
            return;
        }

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownEl.innerText =
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
    };

    setInterval(updateCountdown, 1000);

    // CHECK PAYMENT
    const checkPayment = () => {
        fetch(`/payment/status/${orderId}`)
            .then(res => res.json())
            .then(data => {

                if (data.status === 'paid') {
                    window.location.href = `/payment-success/${orderId}`;
                }

                if (['cancelled','failed','expired','rejected','insufficient_funds']
                    .includes(data.status)) {

                    window.location.href = `/payment-error/${orderId}?reason=${data.status}`;
                }
            });
    };

    setInterval(checkPayment, 3000);
});
