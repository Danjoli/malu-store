@extends('layouts.admin.app')

@section('title', 'Envios')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">
        Envios
    </h1>
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

                                <span class="text-gray-400">
                                    —
                                </span>

                            @endif

                        </td>

                        {{-- Frete --}}
                        <td class="p-3">
                            R$ {{ number_format($shipment->shipping_cost ?? 0, 2, ',', '.') }}
                        </td>

                        {{-- Status --}}
                        <td class="p-3">

                            @include('admin.shipments.index.status', [
                                'shipment' => $shipment
                            ])

                        </td>

                        {{-- Ações --}}
                        <td class="p-3 text-right space-x-2">

                            @include('admin.shipments.index.actions', [
                                'shipment' => $shipment
                            ])

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
