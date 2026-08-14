{{-- PEDIDOS --}}

@if ($orders->count())
    <div class="border-t border-[#f0e5e1] pt-6">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <p class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#2d2928]">
                    Pedidos do Cliente
                </p>

                <p class="mt-1 text-sm text-[#746b68]">
                    Histórico de compras realizadas por este cliente.
                </p>
            </div>

            <span
                class="rounded-full bg-[#fdf0f3] px-3 py-1 text-xs font-bold text-[#b85d70]"
            >
                {{ $orders->count() }}
                {{ $orders->count() === 1 ? 'pedido' : 'pedidos' }}
            </span>
        </div>

        <div class="space-y-4">
            @foreach ($orders as $order)
                @php
                    $statuses = [
                        'pending' => [
                            'Aguardando pagamento',
                            'bg-[#fff6df] text-[#986d16]',
                        ],
                        'paid' => [
                            'Pago',
                            'bg-[#eaf6ef] text-[#27754a]',
                        ],
                        'shipped' => [
                            'Enviado',
                            'bg-[#edf4ff] text-[#3b6199]',
                        ],
                        'delivered' => [
                            'Entregue',
                            'bg-[#f1edfb] text-[#69549c]',
                        ],
                        'cancelled' => [
                            'Cancelado',
                            'bg-[#fdf0f3] text-[#b44259]',
                        ],
                    ];

                    [$statusLabel, $statusClass] = $statuses[$order->status]
                        ?? [
                            ucfirst($order->status),
                            'bg-[#f8f3f1] text-[#746b68]',
                        ];
                @endphp

                <div
                    class="overflow-hidden rounded-xl border border-[#eaded9] bg-white transition hover:border-[#dca3af]"
                >
                    {{-- Cabeçalho do pedido --}}
                    <div
                        class="flex flex-col gap-3 bg-[#fdf8f6] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="font-bold text-[#3e3532]">
                                Pedido #{{ $order->id }}
                            </p>

                            <p class="mt-1 text-xs text-[#857b78]">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 sm:justify-end">
                            <span
                                class="rounded-full px-2.5 py-1 text-xs font-bold {{ $statusClass }}"
                            >
                                {{ $statusLabel }}
                            </span>

                            <p class="font-bold text-[#2d2928]">
                                R$ {{ number_format($order->total, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- Itens do pedido --}}
                    <div class="divide-y divide-[#f0e5e1] px-5">
                        @foreach ($order->items as $item)
                            <div
                                class="flex items-center justify-between gap-4 py-3 text-sm"
                            >
                                <div class="min-w-0">
                                    <p class="font-semibold text-[#443d3b]">
                                        {{ $item->name_snapshot }}
                                    </p>

                                    <p class="mt-1 text-xs text-[#857b78]">
                                        Quantidade: {{ $item->quantity }}

                                        @if ($item->color_snapshot)
                                            · Cor: {{ $item->color_snapshot }}
                                        @endif

                                        @if ($item->size_snapshot)
                                            · Tamanho: {{ $item->size_snapshot }}
                                        @endif
                                    </p>
                                </div>

                                <span class="shrink-0 font-semibold text-[#625956]">
                                    R$
                                    {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Ação --}}
                    <div
                        class="flex justify-end border-t border-[#f0e5e1] bg-[#fffdfc] px-5 py-3"
                    >
                        <a
                            href="{{ route('admin.orders.show', $order) }}"
                            class="text-xs font-bold text-[#b85d70] transition hover:text-[#9f4c5e]"
                        >
                            Ver pedido →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
