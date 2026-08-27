@props(['product'])

<section id="descricao" class="mt-8 border-t border-[#eee6e4] pt-5">
    <div class="flex gap-6 border-b border-[#eee6e4] text-[10px] font-bold uppercase tracking-wide text-stone-700">
        <span class="border-b-2 border-[#d66f7c] pb-3">Descrição</span>
        <span class="pb-3 text-stone-400">Detalhes</span>
        <span class="pb-3 text-stone-400">Composição</span>
        <span class="pb-3 text-stone-400">Avaliações (48)</span>
    </div>

    <p class="max-w-2xl py-5 text-xs leading-6 text-stone-600">
        {{ $product->description }}
    </p>
</section>
