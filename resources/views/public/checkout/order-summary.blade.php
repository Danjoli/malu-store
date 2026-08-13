<!-- RESUMO -->
<div class="h-fit rounded-md border border-[#eee6e4] bg-white p-6 md:p-7">

    <h2 class="store-title mb-6 text-2xl font-semibold">
        Resumo do Pedido
    </h2>

    <!-- ITENS DO PEDIDO -->
    <div class="space-y-4">

        @foreach (($cart?->items ?? []) as $item)

            <div class="flex justify-between gap-4 text-sm text-stone-700">

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
    <div class="mb-2 mt-6 flex justify-between text-sm text-stone-700">

        <span>
            Frete
        </span>

        <span id="valor-frete">
            R$ 0,00
        </span>

    </div>

    <hr class="my-4 border-[#ded5d2]">

    <!-- TOTAL -->
    <div class="mb-6 flex justify-between text-xl font-bold">

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
        class="store-button store-button-primary w-full py-4"
    >
        Finalizar Pedido
    </button>

</div>
