{{-- Resumo --}}
<aside class="h-fit rounded-md border border-[#eee6e4] bg-white p-6 lg:sticky lg:top-24" aria-labelledby="order-summary-title">
    <h2 id="order-summary-title" class="store-title mb-6 text-2xl font-semibold">
        Resumo do Pedido
    </h2>

    {{-- Subtotal --}}
    <div class="flex justify-between text-sm text-stone-700">
        <span>Subtotal</span>
        <span>R$ {{ number_format($subtotal ?? 0, 2, ',', '.') }}</span>
    </div>

    {{-- Frete --}}
    <div class="mb-2 mt-6 flex justify-between text-sm text-stone-700">
        <span>Frete</span>
        <span id="valor-frete">R$ 0,00</span>
    </div>

    <hr class="my-4 border-[#ded5d2]">

    {{-- Total --}}
    <div class="mb-6 flex justify-between text-xl font-bold">
        <span>Total</span>

        <span id="valor-total">
            R$ {{ number_format($total ?? ($subtotal ?? 0), 2, ',', '.') }}
        </span>
    </div>

    {{-- Finalizar pedido --}}
    <button
        type="submit"
        id="checkout-submit"
        class="store-button store-button-primary w-full py-4"
    >
        Finalizar Pedido
    </button>
    <p class="mt-3 text-center text-[11px] leading-relaxed text-stone-400">Ao finalizar, você confirma os dados acima e segue diretamente para a confirmação do pagamento escolhido.</p>
</aside>
