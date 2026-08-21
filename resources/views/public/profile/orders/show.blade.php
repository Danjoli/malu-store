@extends('layouts.public.app')

@section('content')
    <div class="store-container max-w-4xl py-10 md:py-14">
        <h1 class="mb-7 text-3xl font-bold leading-none tracking-tight text-stone-900">
            Pedido #{{ $order->id }}
        </h1>

        {{-- Informações do pedido --}}
        <div class="mb-6 space-y-4 rounded-md border border-[#eee6e4] bg-white p-5 shadow-[0_10px_30px_rgba(63,38,35,.05)] md:p-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- Status do pagamento --}}
                <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
                    <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 2v4m0 12v4m10-10h-4M4 12H0" />
                    </svg>

                    <div>
                        <strong>Status Pagamento:</strong><br>

                        @if ($order->status == 'pending')
                            <span class="font-semibold text-yellow-600">Aguardando pagamento</span>
                        @elseif (in_array($order->status, ['pending_payment', 'expired']))
                            <span class="font-semibold text-yellow-600">Aguardando pagamento</span>
                        @elseif ($order->status == 'paid')
                            <span class="font-semibold text-blue-600">Pago</span>
                        @elseif ($order->status == 'cancelled')
                            <span class="font-semibold text-red-600">Cancelado</span>
                        @else
                            <span>
                                {{ $order->status === 'failed'
                                    ? 'Pagamento recusado'
                                    : ($order->status === 'shipped'
                                        ? 'Pedido enviado'
                                        : ($order->status === 'delivered'
                                            ? 'Pedido entregue'
                                            : 'Em processamento')) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Status da entrega --}}
                <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
                    <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12h18M12 3v18" />
                    </svg>

                    <div>
                        <strong>Status Entrega:</strong><br>

                        @if ($order->shipment)
                            @if ($order->shipment->status == 'pending')
                                <span class="text-gray-600">Aguardando pagamento</span>

                            @elseif ($order->shipment->status == 'waiting_post')
                                <span class="font-semibold text-yellow-600">
                                    Etiqueta gerada — aguardando postagem
                                </span>

                                @if ($order->shipment->tracking_code)
                                    <div class="mt-1 text-sm text-gray-600">
                                        Código de rastreio:
                                        <strong>{{ $order->shipment->tracking_code }}</strong>
                                    </div>
                                @endif

                            @elseif ($order->shipment->status == 'shipped')
                                <span class="font-semibold text-blue-600">Pedido postado</span>

                                @if ($order->shipment->tracking_code)
                                    <div class="mt-1 text-sm text-gray-600">
                                        Código de rastreio:
                                        <strong>{{ $order->shipment->tracking_code }}</strong>
                                    </div>
                                @endif

                            @elseif ($order->shipment->status == 'in_transit')
                                <span class="font-semibold text-indigo-600">Pedido em trânsito</span>

                                @if ($order->shipment->tracking_code)
                                    <div class="mt-1 text-sm text-gray-600">
                                        Código de rastreio:
                                        <strong>{{ $order->shipment->tracking_code }}</strong>
                                    </div>
                                @endif

                            @elseif ($order->shipment->status == 'delivered')
                                <span class="font-semibold text-green-600">Pedido entregue</span>

                            @elseif ($order->shipment->status == 'failed')
                                <span class="font-semibold text-red-600">Falha na entrega</span>

                            @elseif ($order->shipment->status == 'problem')
                                <span class="font-semibold text-orange-600">Problema no envio</span>

                            @elseif ($order->shipment->status == 'cancelled')
                                <span class="font-semibold text-red-600">Envio cancelado</span>

                            @else
                                <span class="text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $order->shipment->status)) }}
                                </span>
                            @endif
                        @else
                            @if ($order->status == 'paid')
                                <span class="font-semibold text-yellow-600">Pagamento aprovado</span>
                            @else
                                <span class="text-gray-600">Aguardando pagamento</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Data do pedido --}}
            <div class="flex items-center space-x-3 rounded-md bg-[#fff8f7] p-4">
                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>

                <div>
                    <strong>Data:</strong> {{ $order->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- Endereço de entrega --}}
            <div class="flex items-start space-x-3 rounded-md bg-[#fff8f7] p-4">
                <svg class="mt-1 h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 11c-4.418 0-8 4-8 8h16c0-4-3.582-8-8-8z" />
                </svg>

                <div>
                    <strong>Endereço de Entrega:</strong><br>
                    <span>Destinatário: {{ $order->recipient_name }}</span><br>
                    <span>Telefone: {{ $order->phone }}</span>

                    <br><br>

                    {{ $order->street }}, {{ $order->number }}<br>

                    @if ($order->complement)
                        {{ $order->complement }}<br>
                    @endif

                    {{ $order->neighborhood }} - {{ $order->city }}/{{ $order->state }}<br>
                    CEP: {{ $order->cep }}
                </div>
            </div>
        </div>

        {{-- Itens do pedido --}}
        <div class="mb-6 rounded-md border border-[#eee6e4] bg-white p-5 md:p-6">
            <h2 class="store-title mb-5 text-2xl font-semibold">
                Itens do Pedido
            </h2>

            @if ($order->items && $order->items->count() > 0)
                @foreach ($order->items as $item)
                    <div class="flex items-center space-x-4 border-b border-[#eee6e4] py-4">
                        @if ($item->image_snapshot)
                            <img
                                src="{{ asset('storage/products/' . $item->image_snapshot) }}"
                                alt="{{ $item->name_snapshot }}"
                                class="h-20 w-16 rounded-sm object-cover"
                            >
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded bg-gray-200 text-xs text-gray-400">
                                Sem imagem
                            </div>
                        @endif

                        <div class="flex-1">
                            <div class="font-semibold">
                                {{ $item->name_snapshot }}
                            </div>

                            @if ($item->color_snapshot)
                                <div class="text-sm text-gray-500">
                                    Cor: {{ $item->color_snapshot }}
                                </div>
                            @endif

                            @if ($item->size_snapshot)
                                <div class="text-sm text-gray-500">
                                    Tamanho: {{ $item->size_snapshot }}
                                </div>
                            @endif

                            <div class="text-sm text-gray-500">
                                Quantidade: {{ $item->quantity }}
                            </div>
                        </div>

                        <div class="text-lg font-bold text-stone-800">
                            R$ {{ number_format($item->price, 2, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-gray-500">
                    Nenhum item encontrado neste pedido.
                </p>
            @endif
        </div>

        {{-- Totais --}}
        <div class="mb-6 flex items-center justify-between rounded-md border border-[#eee6e4] bg-white p-5 md:p-6">
            <a href="{{ route('profile.orders') }}" class="store-button store-button-outline">
                ← Voltar
            </a>

            <div class="text-right">
                <p>
                    <strong>Subtotal:</strong>
                    R$ {{ number_format($order->subtotal, 2, ',', '.') }}
                </p>

                <p>
                    <strong>Frete:</strong>
                    R$ {{ number_format($order->shipping, 2, ',', '.') }}
                </p>

                <p class="mt-2 text-2xl font-bold tracking-tight text-stone-900">
                    <strong>Total:</strong>
                    R$ {{ number_format($order->total, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
@endsection
