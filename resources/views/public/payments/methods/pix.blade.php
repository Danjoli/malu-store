@extends('layouts.payments.app')

@section('title', 'Pagamento via PIX')

@section('content')

<div class="max-w-lg mx-auto bg-white shadow-md rounded-lg p-8 text-center">

    <h2 class="text-2xl font-semibold mb-2">
        Pagamento via PIX
    </h2>

    <p class="text-gray-500 mb-2">
        Pedido #{{ $order->id }}
    </p>

    <div class="bg-yellow-100 text-yellow-800 p-3 rounded-lg mb-6">
        ⚠️ Este pagamento expira em <strong id="countdown">--:--</strong>
    </div>

    <div class="bg-gray-50 p-6 rounded-lg mb-6">

        <p class="text-gray-700 mb-4">
            Escaneie o QR Code abaixo para pagar
        </p>

        <img src="data:image/png;base64,{{ $qr_code_base64 }}" class="w-56 mx-auto">

    </div>

    <div class="text-left">

        <p class="text-gray-700 mb-2">
            Ou use o PIX Copia e Cola
        </p>

        <textarea
            id="pixCode"
            readonly
            class="w-full h-24 border rounded-lg p-3 text-sm"
        >{{ $qr_code }}</textarea>

        <button
            onclick="copiarPix()"
            class="mt-3 w-full bg-green-500 text-white py-2 rounded-lg"
        >
            Copiar código PIX
        </button>

    </div>

</div>

@endsection

@push('payment-scripts')
<script>
    window.PIX_ORDER_ID = @json($order->id);
    window.PIX_EXPIRES_AT = @json(
        $order->expires_at?->toIso8601String()
    );
</script>

@vite('resources/js/payments/pix.js')
@endpush
