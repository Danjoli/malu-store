<span class="px-2 py-1 text-xs rounded

    @if($shipment->status == 'pending')
        bg-gray-200 text-gray-800

    @elseif($shipment->status == 'waiting_post')
        bg-yellow-200 text-yellow-800

    @elseif($shipment->status == 'shipped')
        bg-indigo-200 text-indigo-800

    @elseif($shipment->status == 'in_transit')
        bg-blue-200 text-blue-800

    @elseif($shipment->status == 'delivered')
        bg-green-200 text-green-800

    @elseif($shipment->status == 'failed')
        bg-red-200 text-red-800

    @elseif($shipment->status == 'problem')
        bg-orange-200 text-orange-800

    @elseif($shipment->status == 'cancelled')
        bg-red-300 text-red-900

    @else
        bg-gray-100 text-gray-700
    @endif
">

    @switch($shipment->status)

        @case('pending')
            Aguardando Pagamento
            @break

        @case('waiting_post')
            Aguardando Postagem
            @break

        @case('shipped')
            Postado
            @break

        @case('in_transit')
            Em Trânsito
            @break

        @case('delivered')
            Entregue
            @break

        @case('failed')
            Falha na Entrega
            @break

        @case('problem')
            Problema no Envio
            @break

        @case('cancelled')
            Cancelado
            @break

        @default
            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}

    @endswitch

</span>
