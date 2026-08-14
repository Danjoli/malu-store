<span
    class="rounded-full px-2.5 py-1 text-xs font-bold
        @if ($shipment->status == 'pending')
            bg-[#f8f3f1] text-[#625956]
        @elseif ($shipment->status == 'waiting_post')
            bg-[#fff6df] text-[#986d16]
        @elseif ($shipment->status == 'shipped')
            bg-[#edf4ff] text-[#3b6199]
        @elseif ($shipment->status == 'in_transit')
            bg-[#edf4ff] text-[#3b6199]
        @elseif ($shipment->status == 'delivered')
            bg-[#eaf6ef] text-[#27754a]
        @elseif ($shipment->status == 'failed')
            bg-[#fdf0f3] text-[#b44259]
        @elseif ($shipment->status == 'problem')
            bg-[#fff6df] text-[#986d16]
        @elseif ($shipment->status == 'cancelled')
            bg-[#fdf0f3] text-[#b44259]
        @else
            bg-[#f8f3f1] text-[#625956]
        @endif
    "
>
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
