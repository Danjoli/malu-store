@extends('layouts.app')

@section('content')

<div class="container mx-auto max-w-4xl py-10">

    <h1 class="text-2xl font-bold mb-6">
        Pedido #{{ $order->id }}
    </h1>

    <!-- Informações do Pedido -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6 space-y-4">

        <!-- Status Pagamento e Entrega -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Status Pagamento -->
            <div class="p-4 rounded-lg bg-gray-50 flex items-center space-x-3">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 2v4m0 12v4m10-10h-4M4 12H0"/>
                </svg>
                <div>
                    <strong>Status Pagamento:</strong><br>
                    @if($order->status == 'pending')
                        <span class="text-yellow-600 font-semibold">Aguardando pagamento</span>
                    @elseif($order->status == 'paid')
                        <span class="text-blue-600 font-semibold">Pago</span>
                    @elseif($order->status == 'cancelled')
                        <span class="text-red-600 font-semibold">Cancelado</span>
                    @else
                        <span>{{ ucfirst($order->status) }}</span>
                    @endif
                </div>
            </div>

            <!-- Status Entrega -->
            <div class="p-4 rounded-lg bg-gray-50 flex items-center space-x-3">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12h18M12 3v18"/>
                </svg>
                <div>
                    <strong>Status Entrega:</strong><br>
                    @if($order->shipment)
                        @if($order->shipment->status == 'pending')
                            <span class="text-gray-600">Pendente</span>
                        @elseif($order->shipment->status == 'processing')
                            <span class="text-purple-600 font-semibold">Preparando</span>
                        @elseif($order->shipment->status == 'shipped')
                            <span class="text-purple-600 font-semibold">Enviado</span>
                        @elseif($order->shipment->status == 'delivered')
                            <span class="text-green-600 font-semibold">Entregue</span>
                        @elseif($order->shipment->status == 'cancelled')
                            <span class="text-red-600 font-semibold">Cancelado</span>
                        @else
                            {{ ucfirst($order->shipment->status) }}
                        @endif
                    @else
                        @if($order->status == 'paid')
                            <span class="text-purple-600 font-semibold">Preparando</span>
                        @else
                            <span class="text-gray-600">Pendente</span>
                        @endif
                    @endif
                </div>
            </div>

        </div>

        <!-- Data do Pedido -->
        <div class="p-4 rounded-lg bg-gray-50 flex items-center space-x-3">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div>
                <strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Endereço de Entrega -->
        <div class="p-4 rounded-lg bg-gray-50 flex items-start space-x-3">
            <svg class="w-6 h-6 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c-4.418 0-8 4-8 8h16c0-4-3.582-8-8-8z"/>
            </svg>
            <div>
                <strong>Endereço de Entrega:</strong><br>
                {{ $order->address->street ?? '-' }}, {{ $order->address->number ?? '-' }}<br>
                {{ $order->address->neighborhood ?? '-' }} - {{ $order->address->city ?? '-' }}/{{ $order->address->state ?? '-' }}<br>
                CEP: {{ $order->address->cep ?? '-' }}
            </div>
        </div>

    </div>

    <!-- Itens do Pedido -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">

        <h2 class="text-lg font-semibold mb-4">Itens do Pedido</h2>

        @if($order->items && $order->items->count() > 0)
            @foreach($order->items as $item)
                <div class="flex items-center border-b py-3 space-x-4">

                    <!-- Imagem do produto -->
                    @if($item->image_snapshot)
                        <img src="{{ asset('products/' . $item->image_snapshot) }}" alt="{{ $item->name_snapshot }}" class="w-16 h-16 object-cover rounded">
                    @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                            Sem imagem
                        </div>
                    @endif

                    <!-- Informações do item -->
                    <div class="flex-1">
                        <div class="font-semibold">{{ $item->name_snapshot }}</div>
                        @if($item->color_snapshot)
                            <div class="text-sm text-gray-500">Cor: {{ $item->color_snapshot }}</div>
                        @endif
                        @if($item->size_snapshot)
                            <div class="text-sm text-gray-500">Tamanho: {{ $item->size_snapshot }}</div>
                        @endif
                        <div class="text-sm text-gray-500">Quantidade: {{ $item->quantity }}</div>
                    </div>

                    <!-- Preço -->
                    <div class="font-semibold">
                        R$ {{ number_format($item->price,2,',','.') }}
                    </div>

                </div>
            @endforeach
        @else
            <p class="text-gray-500">Nenhum item encontrado neste pedido.</p>
        @endif

    </div>

    <!-- Totais com botão Voltar -->
    <div class="bg-white shadow rounded-lg p-6 mb-6 flex justify-between items-center">

        <!-- Botão Voltar à esquerda -->
        <a href="{{ route('profile.orders') }}"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
            ← Voltar
        </a>

        <!-- Totais à direita -->
        <div class="text-right">
            <p>
                <strong>Subtotal:</strong> R$ {{ number_format($order->subtotal,2,',','.') }}
            </p>
            <p>
                <strong>Frete:</strong> R$ {{ number_format($order->shipping,2,',','.') }}
            </p>
            <p class="text-xl font-bold mt-2">
                <strong>Total:</strong> R$ {{ number_format($order->total,2,',','.') }}
            </p>
        </div>

    </div>

</div>

@endsection
