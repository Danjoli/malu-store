{{-- ENDEREÇO DE ENTREGA --}}

<div class="bg-white shadow rounded-lg p-6 mt-6">

    <h2 class="text-xl font-semibold mb-4">
        Endereço de Entrega
    </h2>

    <p>
        <strong>Destinatário:</strong>
        {{ $order->recipient_name }}
    </p>

    @if($order->phone)
        <p>
            <strong>Telefone:</strong>
            {{ $order->phone }}
        </p>
    @endif

    @if($order->cpf)
        <p>
            <strong>CPF:</strong>
            {{ $order->cpf }}
        </p>
    @endif

    <p>
        {{ $order->street }}, {{ $order->number }}
    </p>

    @if($order->complement)
        <p>{{ $order->complement }}</p>
    @endif

    <p>{{ $order->neighborhood }}</p>

    <p>
        {{ $order->city }} - {{ $order->state }}
    </p>

    <p>
        CEP: {{ $order->cep }}
    </p>

</div>
