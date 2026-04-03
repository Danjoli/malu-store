@extends('layouts.payment')

@section('title', 'Pagamento via PIX')

@section('content')

<div class="max-w-lg mx-auto bg-white shadow-md rounded-lg p-8 text-center">

    <h2 class="text-2xl font-semibold mb-2">
        Pagamento via PIX
    </h2>

    <p class="text-gray-500 mb-2">
        Pedido #{{ $order->id }}
    </p>

    <!-- AVISO DE EXPIRAÇÃO -->
    <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg mb-6">
        ⚠️ Este pagamento expira em <strong id="countdown">--:--</strong>
    </div>

    <div class="bg-gray-50 p-6 rounded-lg mb-6">

        <p class="text-gray-700 mb-4">
            Escaneie o QR Code abaixo para pagar
        </p>

        <img
            src="data:image/png;base64,{{ $qr_code_base64 }}"
            class="w-56 mx-auto"
        >

    </div>

    <div class="text-left">

        <p class="text-gray-700 mb-2">
            Ou use o PIX Copia e Cola
        </p>

        <textarea
            id="pixCode"
            readonly
            class="w-full h-24 border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
        >{{ $qr_code }}</textarea>

        <button
            onclick="copiarPix()"
            class="mt-3 w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition"
        >
            Copiar código PIX
        </button>

    </div>

    <p class="text-gray-500 text-sm mt-6">
        Após o pagamento, a confirmação pode levar alguns segundos.
    </p>

</div>

<script>
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
</script>

@endsection