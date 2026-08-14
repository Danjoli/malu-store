{{-- Gerar etiqueta --}}
@if (!$shipment->shipment_id && $shipment->order->status === 'paid')
    <form
        action="{{ route('admin.shipments.gerar', $shipment->id) }}"
        method="POST"
        class="inline"
    >
        @csrf

        <button
            type="submit"
            class="rounded bg-green-600 px-3 py-1 text-sm text-white hover:bg-green-700"
        >
            Gerar Etiqueta
        </button>
    </form>
@endif

{{-- Imprimir etiqueta --}}
@if ($shipment->label_url)
    <a
        href="{{ $shipment->label_url }}"
        target="_blank"
        class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700"
    >
        Imprimir Etiqueta
    </a>
@endif

{{-- Editar --}}
@if (!in_array($shipment->status, ['delivered', 'cancelled']))
    <a
        href="{{ route('admin.shipments.edit', $shipment) }}"
        class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600"
    >
        Editar
    </a>
@else
    <span class="cursor-not-allowed rounded bg-gray-300 px-3 py-1 text-sm text-gray-600">
        Bloqueado
    </span>
@endif

{{-- Atualizar status --}}
@if ($shipment->shipment_id && !in_array($shipment->status, ['delivered', 'cancelled']))
    <form
        action="{{ route('admin.shipments.atualizarStatus', $shipment->id) }}"
        method="POST"
        class="inline"
    >
        @csrf

        <button
            type="submit"
            class="rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600"
        >
            Atualizar Status
        </button>
    </form>
@endif
