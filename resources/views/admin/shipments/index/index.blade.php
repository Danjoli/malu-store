@extends('layouts.admin.app')

@section('title', 'Envios')

@section('content')
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Logística</p>
        <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Envios</h1>
        <p class="mt-1 text-sm text-[#746b68]">Acompanhe etiquetas, rastreios e entregas.</p>
    </div>
    <div class="overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-left text-sm">
                <thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]">
                    <tr>
                        <th class="p-4">Pedido</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Transportadora</th>
                        <th class="p-4">Rastreamento</th>
                        <th class="p-4">Frete</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e5e1]">
                    @forelse($shipments as $shipment)
                        <tr class="transition hover:bg-[#fdf8f6]">
                            <td class="p-4 font-bold text-[#3e3532]">#{{ $shipment->order->id }}</td>
                            <td class="p-4 text-[#625956]">{{ $shipment->order->user->name }}</td>
                            <td class="p-4 text-[#625956]">{{ $shipment->carrier ?? '—' }}</td>
                            <td class="p-4 font-mono text-xs font-semibold text-[#625956]">
                                {{ $shipment->tracking_code ?: '—' }}</td>
                            <td class="p-4 font-semibold">R$ {{ number_format($shipment->shipping_cost ?? 0, 2, ',', '.') }}
                            </td>
                            <td class="p-4">@include('admin.shipments.index.status', ['shipment' => $shipment])</td>
                            <td class="p-4 text-right">@include('admin.shipments.index.actions', ['shipment' => $shipment])</td>
                    </tr>@empty<tr>
                            <td colspan="7" class="p-12 text-center text-[#746b68]">Nenhum envio cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
