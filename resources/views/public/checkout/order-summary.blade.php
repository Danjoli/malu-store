<!-- RESUMO -->
<div class="bg-white p-6 rounded-xl shadow h-fit">

    <h2 class="text-xl font-semibold mb-6">
        Resumo do Pedido
    </h2>

    <!-- ITENS DO PEDIDO -->
    <div class="space-y-4">

        @foreach (($cart?->items ?? []) as $item)

            <div class="flex justify-between gap-4">

                <span>
                    {{ $item->name_snapshot }}
                    (x{{ $item->quantity }})
                </span>

                <span class="whitespace-nowrap">
                    R$
                    {{ number_format(
                        $item->price * $item->quantity,
                        2,
                        ',',
                        '.'
                    ) }}
                </span>

            </div>

        @endforeach

    </div>

    <!-- FRETE -->
    <div class="flex justify-between mb-2 mt-6">

        <span>
            Frete
        </span>

        <span id="valor-frete">
            R$ 0,00
        </span>

    </div>

    <hr class="my-4">

    <!-- TOTAL -->
    <div class="flex justify-between font-bold text-lg mb-6">

        <span>
            Total
        </span>

        <span id="valor-total">
            R$
            {{ number_format(
                $total ?? $subtotal ?? 0,
                2,
                ',',
                '.'
            ) }}
        </span>

    </div>

    <!-- FINALIZAR PEDIDO  -->
    <button
        type="submit"
        class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition"
    >
        Finalizar Pedido
    </button>

</div>
