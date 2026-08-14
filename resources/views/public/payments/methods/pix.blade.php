@extends('layouts.payments.app')

@section('title', 'Pagamento via Pix')

@section('content')
    <div
        class="w-full max-w-lg rounded-2xl border border-[#eaded9] bg-white p-6 text-center shadow-[0_12px_34px_rgba(76,50,47,0.08)] sm:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Pedido #{{ $order->id }}</p>
        <h1 class="mt-2 font-['Cormorant_Garamond'] text-3xl font-semibold">Pague com Pix</h1>
        <p class="mt-2 text-sm text-[#746b68]">Escaneie o QR Code ou copie o código abaixo.</p>
        <div class="my-6 rounded-xl bg-[#fff6df] px-4 py-3 text-sm text-[#986d16]">Este pagamento expira em <strong
                id="countdown">--:--</strong></div>
        <div class="rounded-2xl bg-[#fdf8f6] p-5"><img src="data:image/png;base64,{{ $qr_code_base64 }}" class="mx-auto w-56"
                alt="QR Code Pix"></div>
        <div class="mt-6 text-left"><label for="pixCode" class="mb-2 block text-sm font-semibold text-[#443d3b]">Pix Copia
                e Cola</label>
            <textarea id="pixCode" readonly class="h-24 w-full rounded-xl border border-[#ded4d0] p-3 text-xs text-[#625956]">{{ $qr_code }}</textarea><button onclick="copiarPix()" type="button"
                class="mt-3 w-full rounded-xl bg-[#cf7184] py-3 text-sm font-bold text-white hover:bg-[#b85d70]">Copiar
                código Pix</button>
        </div>
    </div>
@endsection

@push('payment-scripts')
    <script>
        window.PIX_ORDER_ID = @json($order->id);
        window.PIX_EXPIRES_AT = @json($order->expires_at?->toIso8601String());
        window.PIX_STATUS_URL = @json(route('payment.status', $order->id));
        window.PIX_SUCCESS_URL = @json(route('payment.success', $order->id));
        window.PIX_ERROR_URL = @json(route('payment.error', $order->id));
    </script>
    @vite('resources/js/payments/pix/index.js')
@endpush
