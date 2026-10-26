@php
    $primaryImage = $product?->images?->first();
@endphp

<div class="grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_minmax(360px,520px)]">
    <div class="order-2 space-y-6 xl:order-1">
        <div>
            <label class="block font-medium" for="productName">Nome do Produto</label>
            <input
                type="text"
                id="productName"
                name="name"
                value="{{ old('name', $product->name ?? '') }}"
                class="w-full rounded border p-2"
            >
        </div>

        <div>
            <label class="block font-medium" for="productCategory">Categoria</label>
            <select id="productCategory" name="category_id" class="w-full rounded border p-2">
                @foreach ($categories as $category)
                    <option
                        value="{{ $category->id }}"
                        @selected(old('category_id', $product->category_id ?? '') == $category->id)
                    >
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-medium" for="productPrice">Preço</label>
            <input
                type="number"
                id="productPrice"
                name="price"
                step="0.01"
                value="{{ old('price', $product->price ?? '') }}"
                class="w-full rounded border p-2"
            >
        </div>

        <div>
            <label class="block font-medium" for="productDescription">Descrição</label>
            <p class="mb-2 text-sm text-[#746b68]">
                Use a imagem ao lado como referência para detalhar tecido, modelagem, cor e acabamento.
            </p>
            <textarea
                id="productDescription"
                name="description"
                rows="7"
                class="w-full rounded border p-2"
            >{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="hidden" name="active" value="0">
                <input
                    type="checkbox"
                    name="active"
                    value="1"
                    @checked(old('active', $product->active ?? false))
                >
                Produto ativo
            </label>
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <label class="text-lg font-bold">Variações (Cor, Tamanho, Estoque)</label>
                <button
                    type="button"
                    id="btn-add-variant"
                    class="rounded bg-green-600 px-3 py-1 text-white"
                >
                    + Adicionar Variação
                </button>
            </div>

            <div id="variants-container" class="mt-3 space-y-3">
                @if (isset($product) && $product && $product->variants)
                    @foreach ($product->variants as $i => $variant)
                        <div class="variant-row grid grid-cols-1 gap-2 rounded border p-3 sm:grid-cols-4">
                            <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                            <input
                                type="text"
                                name="variants[{{ $i }}][color]"
                                value="{{ $variant->color }}"
                                placeholder="Cor"
                                class="rounded border p-2"
                            >
                            <select name="variants[{{ $i }}][size]" class="rounded border p-2">
                                @foreach ($sizes as $size)
                                    <option value="{{ $size->value }}" @selected($variant->size === $size->value)>
                                        {{ $size->value }}
                                    </option>
                                @endforeach
                            </select>
                            <input
                                type="number"
                                name="variants[{{ $i }}][stock]"
                                value="{{ $variant->stock }}"
                                placeholder="Estoque"
                                class="rounded border p-2"
                            >
                            <button
                                type="button"
                                onclick="this.parentElement.remove()"
                                class="rounded bg-red-600 px-3 py-2 text-white"
                            >
                                Remover
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">
                Cancelar
            </a>
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                {{ $button }}
            </button>
        </div>
    </div>

    <aside class="order-1 xl:sticky xl:top-8 xl:order-2" aria-labelledby="product-image-heading">
        <div class="overflow-hidden rounded-2xl border border-[#ddc9c4] bg-white shadow-[0_18px_50px_rgba(76,50,47,0.12)]">
            <div class="border-b border-[#eaded9] px-5 py-4">
                <h2 id="product-image-heading" class="text-lg font-bold text-[#2d2928]">
                    Visualização da roupa
                </h2>
                <p class="mt-1 text-sm text-[#746b68]">
                    Selecione uma miniatura para ampliar. Novas fotos aparecem aqui antes de salvar.
                </p>
            </div>

            <div
                id="productImageStage"
                class="relative flex aspect-[4/5] max-h-[70vh] min-h-[420px] items-center justify-center bg-[#f3ebe7]"
                data-initial-src="{{ $primaryImage?->url }}"
                data-initial-alt="{{ $product?->name ? 'Imagem de '.$product->name : '' }}"
            >
                <img
                    id="productImageStageImage"
                    src="{{ $primaryImage?->url }}"
                    alt="{{ $product?->name ? 'Imagem de '.$product->name : '' }}"
                    class="h-full w-full object-contain {{ $primaryImage ? '' : 'hidden' }}"
                >

                <div
                    id="productImageStageEmpty"
                    class="px-8 text-center text-[#746b68] {{ $primaryImage ? 'hidden' : '' }}"
                >
                    <span class="block text-5xl" aria-hidden="true">▧</span>
                    <p class="mt-4 font-semibold text-[#443d3b]">A imagem grande aparecerá aqui</p>
                    <p class="mt-2 text-sm">Escolha uma ou mais fotos da roupa para começar.</p>
                </div>
            </div>

            <div class="space-y-4 border-t border-[#eaded9] p-5">
                <div>
                    <label for="productImages" class="mb-2 block text-sm font-bold text-[#443d3b]">
                        {{ $product ? 'Selecionar novas imagens' : 'Selecionar imagens' }}
                    </label>
                    <input
                        type="file"
                        name="images[]"
                        id="productImages"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full rounded-lg border border-[#ddc9c4] p-2 text-sm"
                    >
                    @if ($product)
                        <p class="mt-2 text-xs text-[#857b78]">
                            Ao salvar com novas imagens, a galeria atual será substituída.
                        </p>
                    @endif
                </div>

                <div id="imagePreview" class="grid grid-cols-4 gap-2" aria-label="Miniaturas do produto">
                    @foreach ($product?->images ?? [] as $image)
                        <button
                            type="button"
                            class="product-image-thumbnail overflow-hidden rounded-lg border-2 border-transparent bg-[#f3ebe7] transition hover:border-[#cf7184]"
                            data-preview-src="{{ $image->url }}"
                            data-preview-alt="Imagem de {{ $product->name }}"
                            aria-label="Ampliar imagem de {{ $product->name }}"
                        >
                            <img src="{{ $image->url }}" alt="" class="aspect-square w-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>
</div>
