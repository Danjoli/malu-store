@extends('layouts.admin')

@section('title', 'Pedidos')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">
        Pedidos
    </h1>
</div>

<div class="bg-white shadow rounded overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-left">

            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">

                <tr>
                    <th class="p-3">Pedido</th>
                    <th class="p-3">Cliente</th>
                    <th class="p-3">Total</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Data</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>

            </thead>

            <tbody>

                @forelse($orders as $order)

                <tr class="border-t hover:bg-gray-50">

                    <td class="p-3 font-semibold">
                        #{{ $order->id }}
                    </td>

                    <td class="p-3">
                        {{ $order->user->name }}
                    </td>

                    <td class="p-3 font-medium">
                        R$ {{ number_format($order->total,2,',','.') }}
                    </td>

                    <td class="p-3">

                        <span class="px-2 py-1 rounded text-sm
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'paid') bg-green-100 text-green-800
                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status == 'delivered') bg-purple-100 text-purple-800
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                        @endif">

                            {{ ucfirst($order->status) }}

                        </span>

                    </td>

                    <td class="p-3 text-gray-600">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="p-3 text-right">

                        <a href="{{ route('admin.orders.show',$order) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">

                           Ver

                        </a>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">
                        Nenhum pedido encontrado.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>

@endsection
