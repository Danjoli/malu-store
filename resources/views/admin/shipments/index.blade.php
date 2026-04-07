@extends('layouts.admin')

@section('title', 'Envios')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Envios</h1>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">

        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th scope="col" class="p-3">Pedido</th>
                    <th scope="col" class="p-3">Cliente</th>
                    <th scope="col" class="p-3">Transportadora</th>
                    <th scope="col" class="p-3">Rastreamento</th>
                    <th scope="col" class="p-3">Frete</th>
                    <th scope="col" class="p-3">Status</th>
                    <th scope="col" class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($shipments as $shipment)
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="p-3 font-semibold">
                            #{{ $shipment->order->id }}
                        </td>

                        <td class="p-3">
                            {{ $shipment->order->user->name }}
                        </td>

                        <td class="p-3">
                            {{ $shipment->carrier ?? '—' }}
                        </td>

                        {{-- Rastreamento --}}
                        <td class="p-3">
                            @if($shipment->tracking_code)
                                <span class="text-blue-600 font-medium">
                                    {{ $shipment->tracking_code }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Frete --}}
                        <td class="p-3">
                            R$ {{ number_format($shipment->shipping_cost ?? 0, 2, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded
                                @if($shipment->status == 'pending')
                                    bg-yellow-200 text-yellow-800
                                @elseif($shipment->status == 'shipped')
                                    bg-blue-200 text-blue-800
                                @elseif($shipment->status == 'delivered')
                                    bg-green-200 text-green-800
                                @elseif($shipment->status == 'cancelled')
                                    bg-red-200 text-red-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_',' ',$shipment->status)) }}
                            </span>
                        </td>

                        {{-- Ações --}}
                        <td class="p-3 text-right space-x-2">

                            {{-- Gerar Etiqueta --}}
                            @if(!$shipment->tracking_code && $shipment->order->status == 'paid')
                                <form action="{{ route('admin.shipments.gerar', $shipment->id) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                        Gerar Etiqueta
                                    </button>
                                </form>
                            @endif

                            {{-- Imprimir Etiqueta --}}
                            @if($shipment->tracking_code && $shipment->label_url)
                                <a href="{{ $shipment->label_url }}" target="_blank"
                                   class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-sm">
                                    Imprimir
                                </a>
                            @endif

                            {{-- Editar --}}
                            @if($shipment->status !== 'delivered')
                                <a href="{{ route('admin.shipments.edit', $shipment) }}"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                                    Editar
                                </a>
                            @else
                                <span class="bg-gray-300 text-gray-600 px-3 py-1 rounded text-sm cursor-not-allowed">
                                    Bloqueado
                                </span>
                            @endif

                            {{-- Atualizar Status Manual --}}
                            @if($shipment->tracking_code)
                                <form action="{{ route('admin.shipments.atualizarStatus', $shipment->id) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                                        Atualizar Status
                                    </button>
                                </form>
                            @endif

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
