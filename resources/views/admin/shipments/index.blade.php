@extends('layouts.admin')

@section('title', 'Envios')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Envios</h1>

    <a href="{{ route('admin.shipments.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
        + Novo Envio
    </a>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">

        <table class="w-full text-left">

            {{-- Cabeçalho --}}
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="p-3">Pedido</th>
                    <th class="p-3">Cliente</th>
                    <th class="p-3">Transportadora</th>
                    <th class="p-3">Rastreamento</th>
                    <th class="p-3">Frete</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            {{-- Conteúdo --}}
            <tbody>

                @forelse($shipments as $shipment)

                    <tr class="border-t hover:bg-gray-50 transition">

                        {{-- Pedido --}}
                        <td class="p-3 font-semibold">
                            #{{ $shipment->order->id }}
                        </td>

                        {{-- Cliente --}}
                        <td class="p-3">
                            {{ $shipment->order->user->name }}
                        </td>

                        {{-- Transportadora --}}
                        <td class="p-3">
                            {{ $shipment->carrier }}
                        </td>

                        {{-- Código de rastreio --}}
                        <td class="p-3">
                            {{ $shipment->tracking_code ?? '—' }}
                        </td>

                        {{-- Frete --}}
                        <td class="p-3">
                            R$ {{ number_format($shipment->shipping_cost, 2, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded
                                @if($shipment->status == 'pending')
                                    bg-yellow-200 text-yellow-800
                                @elseif($shipment->status == 'processing')
                                    bg-purple-200 text-purple-800
                                @elseif($shipment->status == 'shipped')
                                    bg-blue-200 text-blue-800
                                @elseif($shipment->status == 'delivered')
                                    bg-green-200 text-green-800
                                @elseif($shipment->status == 'cancelled')
                                    bg-red-200 text-red-800
                                @endif
                            ">
                                {{ ucfirst($shipment->status) }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.shipments.edit', $shipment) }}"
                               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                                Editar
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            Nenhum envio cadastrado.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>
</div>

@endsection
