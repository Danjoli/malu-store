@extends('layouts.admin.app')

@section('title', 'Pedido')

@section('content')

<div class="max-w-5xl mx-auto">


<h1 class="text-3xl font-bold mb-6">
    Pedido #{{ $order->id }}
</h1>


<!-- INFORMAÇÕES DO PEDIDO -->
<div class="bg-white shadow rounded-lg p-6 space-y-6">

    <div class="grid grid-cols-2 gap-6">

        <div>
            <p class="text-sm text-gray-500">Cliente</p>
            <p class="font-semibold">{{ $order->user->name }}</p>
            <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-semibold capitalize">
                {{ $order->status }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Subtotal</p>
            <p class="font-semibold">
                R$ {{ number_format($order->subtotal, 2, ',', '.') }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Frete</p>
            <p class="font-semibold">
                R$ {{ number_format($order->shipping, 2, ',', '.') }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Total</p>
            <p class="font-semibold text-lg">
                R$ {{ number_format($order->total, 2, ',', '.') }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">Pagamento</p>
            <p class="font-semibold">
                {{ $order->payment_method ?? '—' }}
            </p>
        </div>

    </div>

</div>


<!-- ITENS DO PEDIDO -->
<div class="bg-white shadow rounded-lg p-6 mt-6">

    <h2 class="text-xl font-semibold mb-4">
        Itens do Pedido
    </h2>

    <div class="space-y-4">

        @foreach($order->items as $item)

        @php
            $product = $item->variant?->product;
            $image = $product?->images?->first();
        @endphp

        <div class="flex justify-between border-b pb-3">

            <div class="flex items-center gap-4">

                @if($image)

                    <img
                        src="{{ asset('storage/products/' . $image->image) }}"
                        alt="{{ $item->name_snapshot }}"
                        class="w-14 h-14 object-cover rounded"
                    >

                @else

                    <div class="w-14 h-14 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-xs text-gray-500">
                            Sem imagem
                        </span>
                    </div>

                @endif

                <div>

                    <p class="font-semibold">
                        {{ $item->name_snapshot }}
                    </p>

                    <p class="text-sm text-gray-500">

                        @if($item->color_snapshot)
                            Cor: {{ $item->color_snapshot }}
                        @endif

                        @if($item->size_snapshot)
                            | Tamanho: {{ $item->size_snapshot }}
                        @endif

                    </p>

                    <p class="text-sm">
                        Qtd: {{ $item->quantity }}
                    </p>

                </div>

            </div>

            <div class="font-semibold">
                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
            </div>

        </div>

        @endforeach

    </div>

</div>


<!-- ENDEREÇO -->
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

<!-- BOTÃO VOLTAR -->
<div class="mt-6">

    <a
        href="{{ route('admin.orders.index') }}"
        class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700"
    >
        Voltar
    </a>

</div>

</div>

@endsection
