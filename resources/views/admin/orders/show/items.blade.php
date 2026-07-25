{{-- ITENS DO PEDIDO --}}

<div class="bg-white shadow rounded-lg p-6 mt-6">

    <h2 class="text-xl font-semibold mb-4">
        Itens do Pedido
    </h2>

    <div class="space-y-4">

        @foreach($order->items as $item)

        @php
            $product = $item->variant?->product;
            $image = $product?->images?->first();
        @endphp

        <div class="flex justify-between border-b pb-3">

            <div class="flex items-center gap-4">

                @if($image)

                    <img
                        src="{{ asset('storage/products/' . $image->image) }}"
                        alt="{{ $item->name_snapshot }}"
                        class="w-14 h-14 object-cover rounded"
                    >

                @else

                    <div class="w-14 h-14 bg-gray-200 rounded flex items-center justify-center">
                        <span class="text-xs text-gray-500">
                            Sem imagem
                        </span>
                    </div>

                @endif

                <div>

                    <p class="font-semibold">
                        {{ $item->name_snapshot }}
                    </p>

                    <p class="text-sm text-gray-500">

                        @if($item->color_snapshot)
                            Cor: {{ $item->color_snapshot }}
                        @endif

                        @if($item->size_snapshot)
                            | Tamanho: {{ $item->size_snapshot }}
                        @endif

                    </p>

                    <p class="text-sm">
                        Qtd: {{ $item->quantity }}
                    </p>

                </div>

            </div>

            <div class="font-semibold">
                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
            </div>

        </div>

        @endforeach

    </div>

</div>
