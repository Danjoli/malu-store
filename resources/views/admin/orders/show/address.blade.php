{{-- ENDEREÇO DE ENTREGA --}}

<div class="mt-6 rounded-lg bg-white p-6 shadow">

    <h2 class="mb-4 text-xl font-semibold">
        Endereço de Entrega
    </h2>

    <p>
        <strong>Destinatário:</strong>
        {{ $order->recipient_name }}
    </p>

    @if ($order->phone)
        <p>
            <strong>Telefone:</strong>
            {{ $order->phone }}
        </p>
    @endif

    @if ($order->cpf)
        <p>
            <strong>CPF:</strong>
            {{ $order->cpf }}
        </p>
    @endif

    <p>
        {{ $order->street }}, {{ $order->number }}
    </p>

    @if ($order->complement)
        <p>
            {{ $order->complement }}
        </p>
    @endif

    <p>
        {{ $order->neighborhood }}
    </p>

    <p>
        {{ $order->city }} - {{ $order->state }}
    </p>

    <p>
        CEP: {{ $order->cep }}
    </p>

</div>
