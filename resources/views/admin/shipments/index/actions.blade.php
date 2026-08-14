{{-- GERAR ETIQUETA --}}
@if (!$shipment->shipment_id && $shipment->order->status === 'paid')
    <form action="{{ route('admin.shipments.gerar', $shipment->id) }}" method="POST" class="inline">

        @csrf

        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
            Gerar Etiqueta
        </button>

    </form>
@endif


{{-- IMPRIMIR ETIQUETA --}}
@if ($shipment->label_url)
    <a href="{{ $shipment->label_url }}" target="_blank"
        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
        Imprimir Etiqueta
    </a>
@endif


{{-- EDITAR --}}
@if (!in_array($shipment->status, ['delivered', 'cancelled']))
    <a href="{{ route('admin.shipments.edit', $shipment) }}"
        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
        Editar
    </a>
@else
    <span class="bg-gray-300 text-gray-600 px-3 py-1 rounded text-sm cursor-not-allowed">
        Bloqueado
    </span>
@endif


{{-- ATUALIZAR STATUS --}}
@if ($shipment->shipment_id && !in_array($shipment->status, ['delivered', 'cancelled']))
    <form action="{{ route('admin.shipments.atualizarStatus', $shipment->id) }}" method="POST" class="inline">

        @csrf

        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
            Atualizar Status
        </button>

    </form>
@endif
