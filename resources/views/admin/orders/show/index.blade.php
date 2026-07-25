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

{{-- ITENS DO PEDIDO --}}
@include('admin.orders.show.items', [
    'items' => $order->items
])

{{-- ENDEREÇO DE ENTREGA --}}
@include('admin.orders.show.address', [
    'order' => $order
])

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
