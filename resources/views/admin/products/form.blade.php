<div class="space-y-6">
    {{-- Imagens --}}
    <div>
        <label class="mb-2 block font-medium">
            Imagens do Produto
        </label>

        <input
            type="file"
            name="images[]"
            id="productImages"
            multiple
            accept="image/*"
            class="w-full rounded border p-2"
        >

        <div
            id="imagePreview"
            class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4"
        ></div>
    </div>

    {{-- Nome --}}
    <div>
        <label class="block font-medium">
            Nome do Produto
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            class="w-full rounded border p-2"
        >
    </div>

    {{-- Categoria --}}
    <div>
        <label class="block font-medium">
            Categoria
        </label>

        <select
            name="category_id"
            class="w-full rounded border p-2"
        >
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

    {{-- Preço --}}
    <div>
        <label class="block font-medium">
            Preço
        </label>

        <input
            type="number"
            name="price"
            step="0.01"
            value="{{ old('price', $product->price ?? '') }}"
            class="w-full rounded border p-2"
        >
    </div>

    {{-- Descrição --}}
    <div>
        <label class="block font-medium">
            Descrição
        </label>

        <textarea
            name="description"
            class="w-full rounded border p-2"
        >{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    {{-- Ativo --}}
    <div>
        <label class="flex items-center gap-2">
            <input
                type="checkbox"
                name="active"
                value="1"
                @checked(old('active', $product->active ?? false))
            >

            Produto ativo
        </label>
    </div>

    {{-- Variações --}}
    <div>
        <div class="flex items-center justify-between">
            <label class="text-lg font-bold">
                Variações (Cor, Tamanho, Estoque)
            </label>

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
                    <div class="variant-row grid grid-cols-4 gap-2 rounded border p-3">
                        <input
                            type="text"
                            name="variants[{{ $i }}][color]"
                            value="{{ $variant->color }}"
                            placeholder="Cor"
                            class="rounded border p-2"
                        >

                        <input
                            type="text"
                            name="variants[{{ $i }}][size]"
                            value="{{ $variant->size }}"
                            placeholder="Tamanho"
                            class="rounded border p-2"
                        >

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
                            class="rounded bg-red-600 text-white"
                        >
                            X
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Botões --}}
    <div class="flex items-center justify-between pt-2">
        <a
            href="{{ route('admin.products.index') }}"
            class="text-gray-600 hover:text-gray-900"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
        >
            {{ $button }}
        </button>
    </div>
</div>
