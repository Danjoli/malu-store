@extends('layouts.public.app')

@section('content')
    <div class="store-container py-10 md:py-14">
        <h1 class="store-title mb-8 text-4xl md:text-5xl">
            Meus Pedidos
        </h1>

        <div class="overflow-x-auto rounded-md border border-[#eee6e4] bg-white shadow-[0_10px_30px_rgba(63,38,35,.05)]">
            <table class="w-full min-w-[760px] text-sm">
                <thead class="border-b border-[#ded5d2] bg-[#fff8f7] text-xs font-bold uppercase tracking-wide text-stone-700">
                    <tr>
                        <th class="p-4 text-left">Pedido</th>
                        <th class="p-4 text-left">Data</th>
                        <th class="p-4 text-left">Valor</th>
                        <th class="p-4 text-left">Status Pagamento</th>
                        <th class="p-4 text-left">Status Entrega</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-t border-[#eee6e4] transition hover:bg-[#fffaf9]">
                            <td class="p-4">#{{ $order->id }}</td>
                            <td class="p-4">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="p-4">R$ {{ number_format($order->total, 2, ',', '.') }}</td>

                            {{-- Status de pagamento --}}
                            <td class="p-4">
                                @if ($order->status == 'pending')
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        Aguardando pagamento
                                    </span>
                                @elseif (in_array($order->status, ['pending_payment', 'expired']))
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                        Aguardando pagamento
                                    </span>
                                @elseif ($order->status == 'paid')
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        Pago
                                    </span>
                                @elseif ($order->status == 'cancelled')
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600">
                                        Cancelado
                                    </span>
                                @else
                                    <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">
                                        {{ $order->status === 'failed'
                                            ? 'Pagamento recusado'
                                            : ($order->status === 'shipped'
                                                ? 'Pedido enviado'
                                                : ($order->status === 'delivered'
                                                    ? 'Pedido entregue'
                                                    : 'Em processamento')) }}
                                    </span>
                                @endif
                            </td>

                            {{-- Status de entrega --}}
                            <td class="p-4">
                                @if ($order->shipment)
                                    @if ($order->shipment->status == 'pending')
                                        <span class="text-gray-600">
                                            Aguardando pagamento
                                        </span>
                                    @elseif ($order->shipment->status == 'waiting_post')
                                        <span class="font-semibold text-yellow-600">
                                            Etiqueta gerada — aguardando postagem
                                        </span>
                                    @elseif ($order->shipment->status == 'shipped')
                                        <span class="font-semibold text-blue-600">
                                            Pedido postado
                                        </span>
                                    @elseif ($order->shipment->status == 'in_transit')
                                        <span class="font-semibold text-indigo-600">
                                            Pedido em trânsito
                                        </span>
                                    @elseif ($order->shipment->status == 'delivered')
                                        <span class="font-semibold text-green-600">
                                            Pedido entregue
                                        </span>
                                    @elseif ($order->shipment->status == 'failed')
                                        <span class="font-semibold text-red-600">
                                            Falha na entrega
                                        </span>
                                    @elseif ($order->shipment->status == 'problem')
                                        <span class="font-semibold text-orange-600">
                                            Problema no envio
                                        </span>
                                    @elseif ($order->shipment->status == 'cancelled')
                                        <span class="font-semibold text-red-600">
                                            Envio cancelado
                                        </span>
                                    @else
                                        <span class="text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $order->shipment->status)) }}
                                        </span>
                                    @endif
                                @else
                                    @if ($order->status == 'paid')
                                        <span class="font-semibold text-yellow-600">
                                            Pagamento aprovado
                                        </span>
                                    @else
                                        <span class="text-gray-600">
                                            Aguardando pagamento
                                        </span>
                                    @endif
                                @endif
                            </td>

                            <td class="p-4">
                                <a
                                    href="{{ route('profile.orders.show', $order->id) }}"
                                    class="text-xs font-bold text-[#bd5564] hover:underline"
                                >
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-stone-500">
                                <p class="store-title text-2xl text-stone-700">
                                    Você ainda não possui pedidos.
                                </p>

                                <a
                                    href="{{ route('catalog.index') }}"
                                    class="store-button store-button-primary mt-5"
                                >
                                    Conhecer a coleção
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
