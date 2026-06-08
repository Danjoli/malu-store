function copiarPix() {
    let textarea = document.getElementById('pixCode');

    textarea.select();
    textarea.setSelectionRange(0, 99999);

    document.execCommand("copy");

    alert("Código PIX copiado!");
}

const orderId = {{ $order->id }};

// DATA DE EXPIRAÇÃO (vinda do backend)
const expiresAt = new Date("{{ $order->pix_expires_at }}").getTime();

// CONTADOR
const countdownEl = document.getElementById('countdown');

const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = expiresAt - now;

    if (distance <= 0) {
        countdownEl.innerText = "00:00";

        // REDIRECIONA AUTOMATICAMENTE
        window.location.href = `/payment-error/${orderId}?reason=expired`;
        return;
    }

    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdownEl.innerText =
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0');
};

// Atualiza contador a cada 1s
setInterval(updateCountdown, 1000);


// VERIFICA PAGAMENTO
const checkPayment = () => {
    fetch(`/payment/status/${orderId}`)
        .then(res => res.json())
        .then(data => {

            console.log('Status:', data.status);

            if (data.status === 'paid') {
                window.location.href = `/payment-success/${orderId}`;
            }

            if (['cancelled','failed','expired','rejected','insufficient_funds'].includes(data.status)) {
                window.location.href = `/payment-error/${orderId}?reason=${data.status}`;
            }

        });
};

// Checa a cada 3 segundos
setInterval(checkPayment, 3000);
