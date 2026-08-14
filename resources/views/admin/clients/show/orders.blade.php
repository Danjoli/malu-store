{{-- PEDIDOS --}}

@if ($orders->count())

    <div class="pt-6 border-t">

        <p class="text-lg font-semibold mb-4">
            Pedidos do Cliente
        </p>

        <div class="space-y-4">

            @foreach ($orders as $order)
                <div class="border rounded-lg p-4">

                    <div class="flex justify-between mb-3">

                        <div>
                            <p class="font-semibold">
                                Pedido #{{ $order->id }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="text-right">

                            <p class="font-semibold">
                                R$ {{ number_format($order->total, 2, ',', '.') }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ ucfirst($order->status) }}
                            </p>

                        </div>

                    </div>

                    {{-- ITENS DO PEDIDO --}}
                    <div class="space-y-2">

                        @foreach ($order->items as $item)
                            <div class="flex justify-between text-sm">

                                <span>
                                    {{ $item->name_snapshot }}
                                    (x{{ $item->quantity }})
                                </span>

                                <span>
                                    R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                                </span>

                            </div>
                        @endforeach

                    </div>

                </div>
            @endforeach

        </div>

    </div>

@endif
