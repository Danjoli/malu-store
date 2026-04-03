@extends('layouts.payment')

@section('title', 'Pagamento via Boleto')

@section('content')

<div class="max-w-lg mx-auto mt-12 p-6">

    <div class="bg-white shadow-lg rounded-xl p-8 text-center">

        <h2 class="text-3xl font-bold mb-2 text-yellow-600">
            📄 Pagamento via Boleto
        </h2>

        <p class="text-gray-500 mb-6">
            Pedido #<strong>{{ $order->id }}</strong>
        </p>

        @if(!empty($boleto_url))

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg mb-6">
                <p class="text-gray-700 mb-4">
                    Seu boleto foi gerado com sucesso! 💳
                </p>

                <a
                    href="{{ $boleto_url }}"
                    target="_blank"
                    class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition"
                >
                    Visualizar Boleto
                </a>
            </div>

            @if(isset($expires_at))
                <p class="text-sm text-red-500 mb-6">
                    ⚠️ Vencimento: {{ \Carbon\Carbon::parse($expires_at)->format('d/m/Y H:i') }}
                </p>
            @endif

        @else

            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg mb-6">
                <p class="text-red-600 font-semibold">
                    Não foi possível gerar o boleto.
                </p>

                <a href="{{ route('payment', $order->id) }}"
                   class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">
                    Tentar novamente
                </a>
            </div>

        @endif

        <div class="text-left text-gray-700 space-y-3 mb-6">
            <p>• Você pode pagar o boleto em qualquer banco, aplicativo bancário ou lotérica.</p>
            <p>• A confirmação do pagamento pode levar até <strong>3 dias úteis</strong>.</p>
            <p>• Após a confirmação, seu pedido será processado automaticamente.</p>
            <p>• Guarde o número do boleto ou o PDF para futuras consultas.</p>
        </div>

        <a href="{{ route('shop.index') }}"
           class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition">
            Continuar Comprando
        </a>

        <p class="text-gray-500 text-sm mt-6">
            Em caso de dúvidas, entre em contato com nosso suporte.
        </p>

    </div>

</div>

@push('scripts')
<script>
    setInterval(async () => {
        try {
            const response = await fetch("{{ route('payment.status', $order->id) }}");
            const data = await response.json();

            if (data.status === 'expired') {
                window.location.href = "{{ route('payment.error', ['order' => $order->id, 'reason' => 'expired']) }}";
            }

            if (data.status === 'paid') {
                window.location.href = "{{ route('payment.success', $order->id) }}";
            }

        } catch (e) {
            console.error('Erro ao verificar status');
        }
    }, 5000);
</script>
@endpush

@endsection

