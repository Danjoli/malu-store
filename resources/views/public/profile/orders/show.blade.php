@extends('layouts.public.app')

@section('content')

<div class="store-container max-w-4xl py-10 md:py-14">

    <h1 class="mb-7 text-3xl font-bold leading-none tracking-tight text-stone-900">
        Pedido #{{ $order->id }}
    </h1>

    <!-- Informações do Pedido -->
    <div class="mb-6 space-y-4 rounded-md border border-[#eee6e4] bg-white p-5 shadow-[0_10px_30px_rgba(63,38,35,.05)] md:p-6">

        <!-- Status Pagamento e Entrega -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Status Pagamento -->
            <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
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
                    @elseif(in_array($order->status, ['pending_payment', 'expired']))
                        <span class="text-yellow-600 font-semibold">Aguardando pagamento</span>
                    @elseif($order->status == 'paid')
                        <span class="text-blue-600 font-semibold">Pago</span>
                    @elseif($order->status == 'cancelled')
                        <span class="text-red-600 font-semibold">Cancelado</span>
                    @else
                        <span>{{ $order->status === 'failed' ? 'Pagamento recusado' : ($order->status === 'shipped' ? 'Pedido enviado' : ($order->status === 'delivered' ? 'Pedido entregue' : 'Em processamento')) }}</span>
                    @endif
                </div>
            </div>

            <!-- Status Entrega -->
            <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12h18M12 3v18"/>
                </svg>
                <div>
                    <strong>Status Entrega:</strong><br>

                    @if($order->shipment)

                        {{-- PENDENTE --}}
                        @if($order->shipment->status == 'pending')

                            <span class="text-gray-600">
                                Aguardando pagamento
                            </span>

                        {{-- ETIQUETA GERADA --}}
                        @elseif($order->shipment->status == 'waiting_post')

                            <span class="text-yellow-600 font-semibold">
                                Etiqueta gerada — aguardando postagem
                            </span>

                            @if($order->shipment->tracking_code)
                                <div class="text-sm text-gray-600 mt-1">
                                    Código de rastreio:
                                    <strong>
                                        {{ $order->shipment->tracking_code }}
                                    </strong>
                                </div>
                            @endif

                        {{-- POSTADO --}}
                        @elseif($order->shipment->status == 'shipped')

                            <span class="text-blue-600 font-semibold">
                                Pedido postado
                            </span>

                            @if($order->shipment->tracking_code)
                                <div class="text-sm text-gray-600 mt-1">
                                    Código de rastreio:
                                    <strong>
                                        {{ $order->shipment->tracking_code }}
                                    </strong>
                                </div>
                            @endif

                        {{-- EM TRÂNSITO --}}
                        @elseif($order->shipment->status == 'in_transit')

                            <span class="text-indigo-600 font-semibold">
                                Pedido em trânsito
                            </span>

                            @if($order->shipment->tracking_code)
                                <div class="text-sm text-gray-600 mt-1">
                                    Código de rastreio:
                                    <strong>
                                        {{ $order->shipment->tracking_code }}
                                    </strong>
                                </div>
                            @endif

                        {{-- ENTREGUE --}}
                        @elseif($order->shipment->status == 'delivered')

                            <span class="text-green-600 font-semibold">
                                Pedido entregue
                            </span>

                        {{-- FALHA --}}
                        @elseif($order->shipment->status == 'failed')

                            <span class="text-red-600 font-semibold">
                                Falha na entrega
                            </span>

                        {{-- PROBLEMA --}}
                        @elseif($order->shipment->status == 'problem')

                            <span class="text-orange-600 font-semibold">
                                Problema no envio
                            </span>

                        {{-- CANCELADO --}}
                        @elseif($order->shipment->status == 'cancelled')

                            <span class="text-red-600 font-semibold">
                                Envio cancelado
                            </span>

                        @else

                            <span class="text-gray-700">
                                {{ ucfirst(str_replace('_', ' ', $order->shipment->status)) }}
                            </span>

                        @endif

                    @else

                        @if($order->status == 'paid')

                            <span class="text-yellow-600 font-semibold">
                                Pagamento aprovado
                            </span>

                        @else

                            <span class="text-gray-600">
                                Aguardando pagamento
                            </span>

                        @endif

                    @endif

                </div>
            </div>

        </div>

        <!-- Data do Pedido -->
        <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <div>
                <strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Endereço de Entrega -->
        <div class="flex items-start space-x-3 rounded-md bg-[#fff8f7] p-4">

            <svg class="w-6 h-6 text-green-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c-4.418 0-8 4-8 8h16c0-4-3.582-8-8-8z"/>
            </svg>

            @php
                $address = $order->user->addresses->first();
            @endphp

            <div>

                <strong>Endereço de Entrega:</strong><br>

                @if($address)

                    <span>
                        Destinatário:
                        {{ $address->recipient_name ?? '-' }}
                    </span>
                    <br>

                    <span>
                        Telefone:
                        {{ $address->phone ?? '-' }}
                    </span>

                    <br><br>

                    {{ $address->street ?? '-' }},
                    {{ $address->number ?? '-' }}

                    <br>

                    @if($address->complement)
                        {{ $address->complement }}
                        <br>
                    @endif

                    {{ $address->neighborhood ?? '-' }}
                    -
                    {{ $address->city ?? '-' }}/{{ $address->state ?? '-' }}

                    <br>

                    CEP:
                    {{ $address->cep ?? '-' }}

                @else

                    <span class="text-gray-500">
                        Nenhum endereço cadastrado.
                    </span>

                @endif

            </div>

        </div>

    </div>

    <!-- Itens do Pedido -->
    <div class="mb-6 rounded-md border border-[#eee6e4] bg-white p-5 md:p-6">

        <h2 class="store-title mb-5 text-2xl font-semibold">Itens do Pedido</h2>

        @if($order->items && $order->items->count() > 0)
            @foreach($order->items as $item)
                <div class="flex items-center space-x-4 border-b border-[#eee6e4] py-4">

                    <!-- Imagem do produto -->
                    @if($item->image_snapshot)
                        <img src="{{ asset('storage/products/' . $item->image_snapshot) }}" alt="{{ $item->name_snapshot }}" class="h-20 w-16 rounded-sm object-cover">
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
                    <div class="text-lg font-bold text-stone-800">
                        R$ {{ number_format($item->price,2,',','.') }}
                    </div>

                </div>
            @endforeach
        @else
            <p class="text-gray-500">Nenhum item encontrado neste pedido.</p>
        @endif

    </div>

    <!-- Totais com botão Voltar -->
    <div class="mb-6 flex items-center justify-between rounded-md border border-[#eee6e4] bg-white p-5 md:p-6">

        <!-- Botão Voltar à esquerda -->
        <a href="{{ route('profile.orders') }}"
        class="store-button store-button-outline">
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
            <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900">
                <strong>Total:</strong> R$ {{ number_format($order->total,2,',','.') }}
            </p>
        </div>

    </div>

</div>

@endsection
