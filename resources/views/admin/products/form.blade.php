<div class="space-y-6">

    {{-- NOME --}}
    <div>
        <label class="block font-medium">Nome do Produto</label>
        <input type="text" name="name"
            value="{{ old('name', $product->name ?? '') }}"
            class="w-full border rounded p-2">
    </div>

    {{-- CATEGORIA --}}
    <div>
        <label class="block font-medium">Categoria</label>
        <select name="category_id" class="w-full border rounded p-2">
            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PREÇO --}}
    <div>
        <label class="block font-medium">Preço</label>
        <input type="number" step="0.01" name="price"
            value="{{ old('price', $product->price ?? '') }}"
            class="w-full border rounded p-2">
    </div>

    {{-- DESCRIÇÃO --}}
    <div>
        <label class="block font-medium">Descrição</label>
        <textarea name="description"
            class="w-full border rounded p-2">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    {{-- ATIVO --}}
    <div>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="active" value="1"
                @checked(old('active', $product->active ?? false))>
            Produto ativo
        </label>
    </div>

    {{-- IMAGENS --}}
    <div>
        <label class="block font-medium">Imagens do Produto</label>
        <input type="file" name="images[]" multiple class="w-full border rounded p-2">
    </div>

    {{-- VARIAÇÕES --}}
    <div>
        <div class="flex justify-between items-center">
            <label class="font-bold text-lg">Variações (Cor, Tamanho, Estoque)</label>
            <button type="button" onclick="addVariant()"
                class="bg-green-600 text-white px-3 py-1 rounded">
                + Adicionar Variação
            </button>
        </div>

        <div id="variants-container" class="space-y-3 mt-3">

            {{-- Se estiver editando, carrega variações existentes --}}
            @if(isset($product) && $product && $product->variants)
                @foreach($product->variants as $i => $variant)
                <div class="variant-row grid grid-cols-4 gap-2 border p-3 rounded">
                    <input type="text" name="variants[{{ $i }}][color]"
                        value="{{ $variant->color }}" placeholder="Cor"
                        class="border p-2 rounded">

                    <input type="text" name="variants[{{ $i }}][size]"
                        value="{{ $variant->size }}" placeholder="Tamanho"
                        class="border p-2 rounded">

                    <input type="number" name="variants[{{ $i }}][stock]"
                        value="{{ $variant->stock }}" placeholder="Estoque"
                        class="border p-2 rounded">

                    <button type="button" onclick="this.parentElement.remove()"
                        class="bg-red-600 text-white rounded">X</button>
                </div>
                @endforeach
            @endif

        </div>
    </div>

    {{-- BOTÕES --}}
    </button>
    <div class="flex justify-between items-center pt-2">
        <a href="{{ route('admin.products.index') }}"
            class="text-gray-600 hover:text-gray-900">
            Cancelar
        </a>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $button }}
        </button>
    </div>

</div>

{{-- SCRIPT PARA ADICIONAR VARIAÇÕES --}}
<script>
let variantIndex = {{ isset($product) && $product ? $product->variants->count() : 0 }};

function addVariant() {
    const container = document.getElementById('variants-container');

    const row = document.createElement('div');
    row.classList.add('variant-row', 'grid', 'grid-cols-4', 'gap-2', 'border', 'p-3', 'rounded');

    row.innerHTML = `
        <input type="text" name="variants[${variantIndex}][color]" placeholder="Cor" class="border p-2 rounded">
        <input type="text" name="variants[${variantIndex}][size]" placeholder="Tamanho" class="border p-2 rounded">
        <input type="number" name="variants[${variantIndex}][stock]" placeholder="Estoque" class="border p-2 rounded">
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 text-white rounded">X</button>
    `;

    container.appendChild(row);
    variantIndex++;
}
</script>
