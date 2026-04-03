@extends('layouts.app')

@section('content')

<div class="container mx-auto max-w-5xl py-10">

    <h1 class="text-2xl font-bold mb-6">
        Meus Pedidos
    </h1>

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-100">
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
                @forelse($orders as $order)
                    <tr class="border-t">
                        <td class="p-4">#{{ $order->id }}</td>
                        <td class="p-4">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="p-4">R$ {{ number_format($order->total,2,',','.') }}</td>

                        <!-- Status de Pagamento -->
                        <td class="p-4">
                            @if($order->status == 'pending')
                                <span class="text-yellow-600 font-semibold">Aguardando pagamento</span>
                            @elseif($order->status == 'paid')
                                <span class="text-blue-600 font-semibold">Pago</span>
                            @elseif($order->status == 'cancelled')
                                <span class="text-red-600 font-semibold">Cancelado</span>
                            @else
                                <span>{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>

                        <!-- Status de Entrega -->
                        <td class="p-4">
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
                                    <span>{{ ucfirst($order->shipment->status) }}</span>
                                @endif
                            @else
                                <!-- Se não houver shipment -->
                                @if($order->status == 'paid')
                                    <span class="text-purple-600 font-semibold">Preparando</span>
                                @else
                                    <span class="text-gray-600">Pendente</span>
                                @endif
                            @endif
                        </td>

                        <td class="p-4">
                            <a href="{{ route('profile.orders.show', $order->id) }}" class="text-pink-600 hover:underline">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            Você ainda não possui pedidos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</div>

@endsection
