@props(['items', 'subtotal'])

<section class="checkout-step" aria-labelledby="checkout-review-title">
    <div id="checkout-review-title">
        <x-public.checkout.step-heading number="5" title="Revisar itens" description="Confira produtos, quantidades e valores antes de finalizar o pedido." />
    </div>

    <div class="divide-y divide-[#eee6e4] border-y border-[#eee6e4]">
        @forelse ($items as $item)
            <article class="grid grid-cols-[5rem_minmax(0,1fr)_4rem_5.5rem] items-center gap-3 py-4 sm:grid-cols-[5rem_minmax(0,1fr)_7rem_7rem] sm:gap-4">
                <div class="h-20 w-20 overflow-hidden rounded-md bg-[#f8f1ed]">
                    @if ($item->image_snapshot)
                        <img
                            src="{{ asset('storage/products/' . $item->image_snapshot) }}"
                            alt="{{ $item->name_snapshot }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center text-center text-[10px] text-stone-400">
                            Sem imagem
                        </div>
                    @endif
                </div>
                <div class="min-w-0">
                    <h3 class="truncate text-sm font-bold text-stone-800">{{ $item->name_snapshot }}</h3>
                    @if ($item->color_snapshot || $item->size_snapshot)
                        <p class="mt-1 text-xs text-stone-500">
                            {{ collect([$item->color_snapshot, $item->size_snapshot])->filter()->join(' · ') }}
                        </p>
                    @endif
                </div>
                <div class="text-right sm:text-left">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Quantidade</span>
                    <span class="mt-1 block text-sm text-stone-700">{{ $item->quantity }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-stone-400">Total do item</span>
                    <strong class="mt-1 block text-sm text-stone-800">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</strong>
                </div>
            </article>
        @empty
            <p class="py-6 text-sm text-stone-500">Seu carrinho está vazio.</p>
        @endforelse
    </div>

    <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('public.cart.index') }}" class="store-button store-button-outline">Voltar ao carrinho e editar</a>
        <p class="text-sm text-stone-500">
            Subtotal
            <strong class="ml-3 text-lg text-stone-900">R$ {{ number_format($subtotal, 2, ',', '.') }}</strong>
        </p>
    </div>
</section>
